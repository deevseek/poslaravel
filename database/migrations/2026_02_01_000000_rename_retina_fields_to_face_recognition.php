<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('retina_signature', 'face_recognition_signature');
            $table->renameColumn('retina_registered_at', 'face_recognition_registered_at');
            $table->renameColumn('retina_scan_path', 'face_recognition_scan_path');
        });

        DB::table('attendances')
            ->where('method', 'retina_scan')
            ->update(['method' => 'face_recognition']);
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('face_recognition_signature', 'retina_signature');
            $table->renameColumn('face_recognition_registered_at', 'retina_registered_at');
            $table->renameColumn('face_recognition_scan_path', 'retina_scan_path');
        });

        DB::table('attendances')
            ->where('method', 'face_recognition')
            ->update(['method' => 'retina_scan']);
    }
};
