import os
from io import BytesIO
import numpy as np
from deepface import DeepFace
from fastapi import FastAPI, File, Form, HTTPException, UploadFile
from PIL import Image

APP_MAX_WIDTH = 800
ALLOWED_CONTENT_TYPES = {"image/jpeg", "image/png"}
ALLOWED_FORMATS = {"JPEG", "PNG"}
DEFAULT_MAX_FILE_SIZE = 5 * 1024 * 1024
DEFAULT_THRESHOLD = 0.8
DEFAULT_MODEL_NAME = "Facenet512"

STORAGE_DIR = os.path.join("storage", "faces")

app = FastAPI()


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

    return _resize_image(image, APP_MAX_WIDTH)


def _extract_single_face(image: Image.Image) -> np.ndarray:
    img_array = np.array(image)
    faces = DeepFace.extract_faces(
        img_path=img_array,
        detector_backend="retinaface",
        enforce_detection=False,
        align=True,
    )

    if not faces:
        raise HTTPException(status_code=422, detail="face not detected")
    if len(faces) > 1:
        raise HTTPException(status_code=422, detail="multiple faces detected")

    face = faces[0].get("face")
    if face is None:
        raise HTTPException(status_code=422, detail="face not detected")

    return face


def _embedding_from_face(face: np.ndarray) -> np.ndarray:
    model_name = os.getenv("FACE_MODEL_NAME", DEFAULT_MODEL_NAME)
    embedding_objs = DeepFace.represent(
        img_path=face,
        model_name=model_name,
        detector_backend="skip",
        enforce_detection=False,
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


@app.post("/register-face")
def register_face(user_id: str = Form(...), image: UploadFile = File(...)):
    try:
        pil_image = _load_image(image)
        face = _extract_single_face(pil_image)
        embedding = _embedding_from_face(face)

        _ensure_storage_dir()
        np.save(_embedding_path(user_id), embedding)

        return {"status": "registered", "user_id": user_id}
    except HTTPException:
        raise
    except Exception:
        raise HTTPException(status_code=500, detail="internal error")


@app.post("/verify-face")
def verify_face(user_id: str = Form(...), image: UploadFile = File(...)):
    embedding_path = _embedding_path(user_id)
    if not os.path.exists(embedding_path):
        raise HTTPException(status_code=404, detail="face not registered")

    try:
        pil_image = _load_image(image)
        face = _extract_single_face(pil_image)
        embedding = _embedding_from_face(face)
        stored_embedding = np.load(embedding_path)

        similarity = _cosine_similarity(embedding, stored_embedding)
        confidence = float(np.clip(similarity, 0.0, 1.0))
        matched = confidence >= _get_threshold()

        return {"matched": matched, "confidence": confidence}
    except HTTPException:
        raise
    except Exception:
        raise HTTPException(status_code=500, detail="internal error")
