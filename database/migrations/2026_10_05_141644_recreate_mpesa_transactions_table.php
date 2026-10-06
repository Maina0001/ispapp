<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The old table has only id/created_at/updated_at. Drop and rebuild.
        Schema::dropIfExists('mpesa_transactions');

        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->id();

            // Tenancy + customer link
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedBigInteger('plan_id')->nullable()->index();

            // Safaricom identifiers
            $table->string('merchant_request_id')->nullable()->index();
            $table->string('checkout_request_id')->nullable()->unique();
            $table->string('mpesa_receipt_number')->nullable()->unique();

            // Payment details
            $table->string('phone', 20)->nullable();
            $table->decimal('amount', 12, 2)->default(0);

            // Result / status
            $table->string('result_code')->nullable();
            $table->string('result_desc')->nullable();
            $table->string('status', 20)->default('pending')->index();
            // statuses: pending | completed | failed

            // Raw Safaricom payload for audit
            $table->longText('raw_payload')->nullable();

            // When M-Pesa says the payment happened
            $table->timestamp('transaction_date')->nullable();

            $table->timestamps();

            // Composite index for tenant-scoped queries
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};