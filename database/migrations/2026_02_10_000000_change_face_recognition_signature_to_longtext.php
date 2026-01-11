<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE employees MODIFY face_recognition_signature LONGTEXT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE employees ALTER COLUMN face_recognition_signature TYPE TEXT');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE employees MODIFY face_recognition_signature VARCHAR(255) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE employees ALTER COLUMN face_recognition_signature TYPE VARCHAR(255)');
        }
    }
};
