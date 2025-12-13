<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->unsignedBigInteger('reference_id')->nullable()->after('recorded_at');
            $table->string('reference_type')->nullable()->after('reference_id');
            $table->string('source')->nullable()->after('reference_type');
            $table->foreignId('created_by')->nullable()->after('source')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['reference_id', 'reference_type', 'source']);
        });
    }
};
