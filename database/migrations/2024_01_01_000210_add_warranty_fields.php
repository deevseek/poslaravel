<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('warranty_days')->default(0)->after('stock');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->integer('warranty_days')->default(0)->after('service_fee');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('invoice_number')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('warranty_days');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('warranty_days');
        });
    }
};
