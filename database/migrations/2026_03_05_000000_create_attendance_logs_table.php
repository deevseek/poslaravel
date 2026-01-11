<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->float('confidence');
            $table->timestamp('captured_at');
            $table->string('ip_address')->nullable();
            $table->text('device_info')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'captured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
