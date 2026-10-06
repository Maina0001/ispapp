<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            $table->unsignedBigInteger('customer_id')->nullable()->after('tenant_id');
            $table->decimal('amount', 12, 2)->default(0)->after('customer_id');
            $table->string('currency', 3)->default('KES')->after('amount');
            $table->string('gateway', 32)->nullable()->after('currency');
            $table->string('gateway_reference')->nullable()->unique()->after('gateway');
            $table->unsignedBigInteger('plan_id')->nullable()->after('gateway_reference');
            $table->string('status', 32)->default('pending')->after('plan_id');
            $table->timestamp('paid_at')->nullable()->after('status');

            $table->index(['tenant_id', 'customer_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['gateway_reference']);
            $table->dropIndex(['tenant_id', 'customer_id']);
            $table->dropIndex(['status']);
            $table->dropColumn([
                'tenant_id', 'customer_id', 'amount', 'currency',
                'gateway', 'gateway_reference', 'plan_id', 'status', 'paid_at',
            ]);
        });
    }
};