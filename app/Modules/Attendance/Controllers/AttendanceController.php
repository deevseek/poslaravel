<?php

namespace App\Modules\Attendance\Controllers;

use App\Modules\Attendance\Models\Attendance;
use App\Modules\Attendance\Services\FaceVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AttendanceController
{
    public function __construct(
        private readonly FaceVerificationService $faceVerificationService,
    ) {
    }

    public function checkIn(Request $request): JsonResponse
    {
        return $this->handleAttendance($request, 'checkin');
    }

    public function checkOut(Request $request): JsonResponse
    {
        return $this->handleAttendance($request, 'checkout');
    }

    private function handleAttendance(Request $request, string $type): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        try {
            $verification = $this->faceVerificationService->verify(
                $validated['image'],
                $request->user()?->id,
            );
        } catch (Throwable $exception) {
            return response()->json([
                'matched' => false,
                'message' => 'Face verification failed.',
            ], 502);
        }

        $threshold = (float) config('attendance.confidence_threshold');

        if (! $verification->matched || $verification->confidence < $threshold) {
            return response()->json([
                'matched' => false,
                'confidence' => $verification->confidence,
                'message' => 'Face not recognized.',
            ], 422);
        }

        $attendance = Attendance::create([
            'user_id' => $request->user()->id,
            'type' => $type,
            'confidence' => $verification->confidence,
            'captured_at' => now(),
            'ip_address' => $request->ip(),
            'device_info' => (string) $request->userAgent(),
            'created_at' => now(),
        ]);

        return response()->json([
            'matched' => true,
            'confidence' => $attendance->confidence,
            'attendance_id' => $attendance->id,
            'type' => $attendance->type,
            'captured_at' => $attendance->captured_at?->toIso8601String(),
        ], 201);
    }
}
