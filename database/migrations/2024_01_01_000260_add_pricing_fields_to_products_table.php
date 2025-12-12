<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('pricing_mode', ['manual', 'percentage'])->default('manual')->after('cost_price');
            $table->decimal('margin_percentage', 5, 2)->nullable()->after('pricing_mode');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['pricing_mode', 'margin_percentage']);
        });
    }
};
