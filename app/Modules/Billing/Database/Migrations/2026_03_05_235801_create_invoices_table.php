<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index(); // Multi-tenant boundary
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2);
            
            $table->enum('status', ['unpaid', 'paid', 'cancelled', 'overdue'])->default('unpaid');
            $table->timestamp('due_date');
            $table->timestamp('paid_at')->nullable();
            
            // Reference to the M-Pesa transaction that settled this
            $table->string('mpesa_receipt')->nullable()->index(); 
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('invoices');
    }
};