import logging
import os
from io import BytesIO

import numpy as np
from deepface import DeepFace
from fastapi import FastAPI, File, Form, HTTPException, UploadFile
from fastapi.responses import JSONResponse
from PIL import Image

APP_MAX_WIDTH = 800
ALLOWED_CONTENT_TYPES = {"image/jpeg", "image/png"}
ALLOWED_FORMATS = {"JPEG", "PNG"}
DEFAULT_MAX_FILE_SIZE = 5 * 1024 * 1024
DEFAULT_THRESHOLD = 0.8
DEFAULT_MODEL_NAME = "Facenet512"
DEFAULT_DETECTOR = "opencv"

STORAGE_DIR = os.path.join("storage", "faces")

app = FastAPI()
logging.basicConfig(level=os.getenv("LOG_LEVEL", "INFO"))
logger = logging.getLogger("face_recognition_service")


class FaceDetectionError(Exception):
    def __init__(self, error: str, faces_detected: int):
        super().__init__(error)
        self.error = error
        self.faces_detected = faces_detected


def _get_env_float(name: str, default: float) -> float:
    value = os.getenv(name)
    if value is None:
        return default
    try:
        return float(value)
    except ValueError:
        return default


def _get_env_int(name: str, default: int) -> int:
    value = os.getenv(name)
    if value is None:
        return default
    try:
        return int(value)
    except ValueError:
        return default


def _resize_image(image: Image.Image, max_width: int) -> Image.Image:
    if image.width <= max_width:
        return image
    ratio = max_width / float(image.width)
    new_height = int(image.height * ratio)
    return image.resize((max_width, new_height), Image.LANCZOS)


def _load_image(upload: UploadFile) -> Image.Image:
    if upload.content_type not in ALLOWED_CONTENT_TYPES:
        raise HTTPException(status_code=400, detail="invalid image")

    max_size = _get_env_int("MAX_FILE_SIZE_BYTES", DEFAULT_MAX_FILE_SIZE)
    data = upload.file.read()
    if len(data) > max_size:
        raise HTTPException(status_code=400, detail="invalid image")

    try:
        image = Image.open(BytesIO(data))
        if image.format not in ALLOWED_FORMATS:
            raise HTTPException(status_code=400, detail="invalid image")
        image = image.convert("RGB")
    except HTTPException:
        raise
    except Exception as exc:
        raise HTTPException(status_code=400, detail="invalid image") from exc

    resized = _resize_image(image, APP_MAX_WIDTH)
    logger.info("Image resolution: %sx%s", resized.width, resized.height)
    return resized


def _detect_faces(image: Image.Image) -> list[dict]:
    img_array = np.array(image)
    faces = DeepFace.extract_faces(
        img_path=img_array,
        detector_backend=DEFAULT_DETECTOR,
        enforce_detection=False,
        align=True,
    )
    logger.info(
        "Detector: %s | Faces detected: %s",
        DEFAULT_DETECTOR,
        len(faces),
    )
    return faces


def _extract_single_face(image: Image.Image) -> tuple[np.ndarray, int]:
    faces = _detect_faces(image)
    faces_detected = len(faces)

    if faces_detected == 0:
        raise FaceDetectionError("no_face_detected", 0)
    if faces_detected > 1:
        raise FaceDetectionError("multiple_faces_detected", faces_detected)

    try:
        face = DeepFace.detectFace(
            img_path=np.array(image),
            detector_backend=DEFAULT_DETECTOR,
            enforce_detection=True,
            align=True,
        )
    except Exception as exc:
        raise FaceDetectionError("no_face_detected", 0) from exc

    if face is None:
        raise FaceDetectionError("no_face_detected", 0)

    return face, faces_detected


def _embedding_from_face(face: np.ndarray) -> np.ndarray:
    model_name = os.getenv("FACE_MODEL_NAME", DEFAULT_MODEL_NAME)
    embedding_objs = DeepFace.represent(
        img_path=face,
        model_name=model_name,
        detector_backend="skip",
        enforce_detection=True,
    )

    if not embedding_objs:
        raise HTTPException(status_code=500, detail="internal error")

    embedding = embedding_objs[0].get("embedding")
    if embedding is None:
        raise HTTPException(status_code=500, detail="internal error")

    return np.array(embedding, dtype=np.float32)


def _cosine_similarity(a: np.ndarray, b: np.ndarray) -> float:
    denom = np.linalg.norm(a) * np.linalg.norm(b)
    if denom == 0:
        return 0.0
    return float(np.dot(a, b) / denom)


def _get_threshold() -> float:
    threshold = _get_env_float("FACE_MATCH_THRESHOLD", DEFAULT_THRESHOLD)
    if threshold <= 0 or threshold > 1:
        return DEFAULT_THRESHOLD
    return threshold


def _ensure_storage_dir() -> None:
    os.makedirs(STORAGE_DIR, exist_ok=True)


def _embedding_path(user_id: str) -> str:
    return os.path.join(STORAGE_DIR, f"{user_id}.npy")


def _error_response(error: str, faces_detected: int) -> JSONResponse:
    return JSONResponse(
        status_code=422,
        content={"matched": False, "faces_detected": faces_detected, "error": error},
    )


def _load_embeddings() -> list[tuple[str, np.ndarray]]:
    if not os.path.isdir(STORAGE_DIR):
        return []

    embeddings: list[tuple[str, np.ndarray]] = []
    for filename in os.listdir(STORAGE_DIR):
        if not filename.endswith(".npy"):
            continue
        user_id = os.path.splitext(filename)[0]
        path = os.path.join(STORAGE_DIR, filename)
        try:
            embeddings.append((user_id, np.load(path)))
        except Exception:
            logger.warning("Failed to load embedding for user %s", user_id)

    return embeddings


@app.post("/register-face")
def register_face(user_id: str = Form(...), image: UploadFile = File(...)):
    try:
        pil_image = _load_image(image)
        face, faces_detected = _extract_single_face(pil_image)
        embedding = _embedding_from_face(face)

        _ensure_storage_dir()
        np.save(_embedding_path(user_id), embedding)

        return {"status": "registered", "user_id": user_id, "faces_detected": faces_detected}
    except FaceDetectionError as exc:
        return _error_response(exc.error, exc.faces_detected)
    except HTTPException:
        raise
    except Exception:
        raise HTTPException(status_code=500, detail="internal error")


@app.post("/verify-face")
def verify_face(user_id: str = Form(...), image: UploadFile = File(...)):
    embedding_path = _embedding_path(user_id)
    if not os.path.exists(embedding_path):
        return JSONResponse(
            status_code=404,
            content={"matched": False, "faces_detected": 0, "error": "face_not_registered"},
        )

    try:
        pil_image = _load_image(image)
        face, faces_detected = _extract_single_face(pil_image)
        embedding = _embedding_from_face(face)
        stored_embedding = np.load(embedding_path)

        similarity = _cosine_similarity(embedding, stored_embedding)
        confidence = float(np.clip(similarity, 0.0, 1.0))
        matched = confidence >= _get_threshold()

        return {
            "matched": matched,
            "confidence": confidence,
            "faces_detected": faces_detected,
        }
    except FaceDetectionError as exc:
        return _error_response(exc.error, exc.faces_detected)
    except HTTPException:
        raise
    except Exception:
        raise HTTPException(status_code=500, detail="internal error")


@app.post("/identify-face")
def identify_face(image: UploadFile = File(...)):
    try:
        pil_image = _load_image(image)
        face, faces_detected = _extract_single_face(pil_image)
        embedding = _embedding_from_face(face)

        embeddings = _load_embeddings()
        if not embeddings:
            return JSONResponse(
                status_code=404,
                content={
                    "matched": False,
                    "faces_detected": faces_detected,
                    "error": "no_registered_faces",
                },
            )

        best_user_id = None
        best_similarity = 0.0
        for user_id, stored_embedding in embeddings:
            similarity = _cosine_similarity(embedding, stored_embedding)
            if similarity > best_similarity:
                best_similarity = similarity
                best_user_id = user_id

        confidence = float(np.clip(best_similarity, 0.0, 1.0))
        matched = confidence >= _get_threshold()

        if not matched or best_user_id is None:
            return JSONResponse(
                status_code=404,
                content={
                    "matched": False,
                    "faces_detected": faces_detected,
                    "confidence": confidence,
                    "error": "face_not_matched",
                },
            )

        return {
            "matched": True,
            "faces_detected": faces_detected,
            "confidence": confidence,
            "user_id": best_user_id,
        }
    except FaceDetectionError as exc:
        return _error_response(exc.error, exc.faces_detected)
    except HTTPException:
        raise
    except Exception:
        raise HTTPException(status_code=500, detail="internal error")
