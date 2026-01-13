import logging
import os
import time
from typing import Optional

import cv2
import numpy as np
from deepface import DeepFace
from fastapi import FastAPI, File, Form, UploadFile
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse

DEFAULT_MODEL_NAME = "Facenet512"
DEFAULT_DETECTOR = "opencv"
DEFAULT_THRESHOLD = 0.8

EMBEDDING_STORAGE_DIR = os.path.join("storage", "faces")

app = FastAPI()
logging.basicConfig(level=os.getenv("LOG_LEVEL", "INFO"))
logger = logging.getLogger("face_recognition_service")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://127.0.0.1:8000"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


def _get_threshold() -> float:
    value = os.getenv("FACE_MATCH_THRESHOLD")
    if value is None:
        return DEFAULT_THRESHOLD
    try:
        threshold = float(value)
    except ValueError:
        return DEFAULT_THRESHOLD
    if threshold <= 0 or threshold > 1:
        return DEFAULT_THRESHOLD
    return threshold


def _read_image(upload: UploadFile) -> np.ndarray:
    data = upload.file.read()
    img_array = np.frombuffer(data, np.uint8)
    bgr = cv2.imdecode(img_array, cv2.IMREAD_COLOR)
    if bgr is None:
        raise ValueError("invalid image")
    rgb = cv2.cvtColor(bgr, cv2.COLOR_BGR2RGB)
    return rgb


def _detect_faces(image_rgb: np.ndarray) -> list[dict]:
    try:
        faces = DeepFace.extract_faces(
            img_path=image_rgb,
            detector_backend=DEFAULT_DETECTOR,
            enforce_detection=True,
            align=True,
        )
    except ValueError as exc:
        logger.info("face detection failed: %s", exc)
        return []
    return faces


def _embedding_from_face(face_rgb: np.ndarray) -> np.ndarray:
    model_name = os.getenv("FACE_MODEL_NAME", DEFAULT_MODEL_NAME)
    embeddings = DeepFace.represent(
        img_path=face_rgb,
        model_name=model_name,
        detector_backend="skip",
        enforce_detection=False,
    )
    if not embeddings:
        raise ValueError("embedding failed")
    embedding = embeddings[0].get("embedding")
    if embedding is None:
        raise ValueError("embedding failed")
    return np.array(embedding, dtype=np.float32)


def _cosine_similarity(a: np.ndarray, b: np.ndarray) -> float:
    denom = np.linalg.norm(a) * np.linalg.norm(b)
    if denom == 0:
        return 0.0
    return float(np.dot(a, b) / denom)


def _embedding_path(employee_id: str) -> str:
    return os.path.join(EMBEDDING_STORAGE_DIR, f"{employee_id}.npy")


def _load_embedding(employee_id: str) -> Optional[np.ndarray]:
    path = _embedding_path(employee_id)
    if not os.path.exists(path):
        return None
    return np.load(path)


def _success_response(matched: bool, confidence: Optional[float], faces_detected: int) -> dict:
    payload = {"matched": matched, "faces_detected": faces_detected}
    if confidence is not None:
        payload["confidence"] = confidence
    return payload


def _error_response(
    error: str,
    faces_detected: Optional[int],
    detail: Optional[str] = None,
    status_code: int = 422,
) -> JSONResponse:
    payload = {"matched": False, "faces_detected": faces_detected, "error": error}
    if detail:
        payload["detail"] = detail
    return JSONResponse(status_code=status_code, content=payload)


@app.get("/health")
def health():
    return {"ok": True}


@app.post("/verify-face")
def verify_face(
    image: Optional[UploadFile] = File(None),
    file: Optional[UploadFile] = File(None),
    employee_id: Optional[str] = Form(None),
    user_id: Optional[str] = Form(None),
):
    started_at = time.perf_counter()
    upload = image or file
    if upload is None:
        return _error_response("internal_error", None, "image is required")

    logger.info("verify-face request received")
    logger.info("upload filename=%s content_type=%s", upload.filename, upload.content_type)

    try:
        image_rgb = _read_image(upload)
        faces = _detect_faces(image_rgb)
        faces_detected = len(faces)
        logger.info("faces_detected=%s", faces_detected)

        if faces_detected == 0:
            return _error_response("no_face_detected", 0)

        face_rgb = faces[0].get("face") if faces else None
        if face_rgb is None:
            return _error_response("no_face_detected", 0)

        embedding = _embedding_from_face(face_rgb)
        stored_embedding = None
        lookup_id = employee_id or user_id
        if lookup_id:
            stored_embedding = _load_embedding(lookup_id)

        if stored_embedding is None:
            confidence = 0.0
            return JSONResponse(
                status_code=200,
                content={
                    "matched": False,
                    "faces_detected": faces_detected,
                    "confidence": confidence,
                    "error": "not_matched",
                },
            )

        similarity = _cosine_similarity(embedding, stored_embedding)
        confidence = float(np.clip(similarity, 0.0, 1.0))
        matched = confidence >= _get_threshold()

        if not matched:
            return JSONResponse(
                status_code=200,
                content={
                    "matched": False,
                    "faces_detected": faces_detected,
                    "confidence": confidence,
                    "error": "not_matched",
                },
            )

        return _success_response(True, confidence, faces_detected)
    except Exception as exc:
        logger.exception("verify-face failed")
        return _error_response("internal_error", None, str(exc), status_code=500)
    finally:
        elapsed_ms = (time.perf_counter() - started_at) * 1000
        logger.info("verify-face processing_ms=%.2f", elapsed_ms)


@app.post("/register-face")
def register_face(
    image: UploadFile = File(...),
    user_id: Optional[str] = Form(None),
    employee_id: Optional[str] = Form(None),
):
    lookup_id = user_id or employee_id
    if not lookup_id:
        return _error_response("invalid_request", None, "user_id is required")

    try:
        image_rgb = _read_image(image)
        faces = _detect_faces(image_rgb)
        faces_detected = len(faces)
        logger.info("register-face faces_detected=%s", faces_detected)

        if faces_detected == 0:
            return _error_response("no_face_detected", 0)

        face_rgb = faces[0].get("face")
        if face_rgb is None:
            return _error_response("no_face_detected", 0)

        embedding = _embedding_from_face(face_rgb)
        os.makedirs(EMBEDDING_STORAGE_DIR, exist_ok=True)
        np.save(_embedding_path(lookup_id), embedding)

        return {"status": "registered", "user_id": lookup_id, "faces_detected": faces_detected}
    except Exception as exc:
        logger.exception("register-face failed")
        return _error_response("internal_error", None, str(exc), status_code=500)


@app.post("/identify-face")
def identify_face(image: UploadFile = File(...)):
    try:
        image_rgb = _read_image(image)
        faces = _detect_faces(image_rgb)
        faces_detected = len(faces)
        logger.info("identify-face faces_detected=%s", faces_detected)

        if faces_detected == 0:
            return _error_response("no_face_detected", 0)

        face_rgb = faces[0].get("face")
        if face_rgb is None:
            return _error_response("no_face_detected", 0)

        embedding = _embedding_from_face(face_rgb)

        if not os.path.isdir(EMBEDDING_STORAGE_DIR):
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
        for filename in os.listdir(EMBEDDING_STORAGE_DIR):
            if not filename.endswith(".npy"):
                continue
            user_id = os.path.splitext(filename)[0]
            try:
                stored_embedding = np.load(os.path.join(EMBEDDING_STORAGE_DIR, filename))
            except Exception:
                continue

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
    except Exception as exc:
        logger.exception("identify-face failed")
        return _error_response("internal_error", None, str(exc), status_code=500)
