<?php

namespace App\Console\Commands;

use App\Services\FaceRecognitionService;
use Illuminate\Console\Command;

class FaceApiCheckCommand extends Command
{
    protected $signature = 'faceapi:check';
    protected $description = 'Check the health of the Face API service.';

    public function __construct(private readonly FaceRecognitionService $faceRecognitionService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->faceRecognitionService->health()) {
            $this->info('Face API OK');

            return self::SUCCESS;
        }

        $reason = $this->faceRecognitionService->lastHealthError() ?? 'unknown';
        $this->error("Face API UNAVAILABLE: {$reason}");

        return self::FAILURE;
    }
}
