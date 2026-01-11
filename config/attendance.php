<?php

return [
    'face_api_url' => env('FACE_API_URL', 'http://127.0.0.1:8001/verify-face'),
    'face_api_health_url' => env('FACE_API_HEALTH_URL', 'http://127.0.0.1:8001/health'),
    'timeout' => (int) env('FACE_API_TIMEOUT', 20),
    'confidence_threshold' => (float) env('FACE_CONFIDENCE_THRESHOLD', 0.8),
];
