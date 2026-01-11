<?php

return [
    'face_api_url' => env('FACE_API_URL', 'https://face-api.example.com/verify'),
    'confidence_threshold' => (float) env('FACE_CONFIDENCE_THRESHOLD', 0.8),
    'timeout' => (int) env('FACE_API_TIMEOUT', 10),
];
