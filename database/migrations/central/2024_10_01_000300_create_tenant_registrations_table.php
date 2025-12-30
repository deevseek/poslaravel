<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenant_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subdomain')->unique();
            $table->string('admin_name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->foreignId('plan_id')->nullable()->constrained('subscription_plans');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('payment_method', ['transfer', 'e-wallet', 'cash']);
            $table->string('payment_reference')->nullable();
            $table->decimal('payment_amount', 12, 2)->default(0);
            $table->string('payment_proof_path')->nullable();
            $table->text('password_encrypted');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_registrations');
    }
};
