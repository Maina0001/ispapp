<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            
            // Identification
            $table->string('name');
            $table->string('phone_number')->unique()->index(); // Used for M-Pesa & Radius Username
            $table->string('email')->nullable()->unique();
            
            // Service Details
            $table->enum('status', ['active', 'suspended', 'expired'])->default('expired');
            $table->decimal('wallet_balance', 10, 2)->default(0.00);
            $table->timestamp('last_payment_at')->nullable();
            $table->timestamp('service_expiry_at')->nullable();
            
            // Metadata
            $table->string('mac_address', 17)->nullable(); // Bind to specific device if needed
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};