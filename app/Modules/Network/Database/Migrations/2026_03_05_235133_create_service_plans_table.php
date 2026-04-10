<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('service_plans', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tenant_id')->index();
    $table->string('name'); // e.g., "1 Hour Unlimited", "Daily 10Mbps"
    $table->integer('duration_minutes'); // How long it lasts
    $table->decimal('price', 10, 2);
    $table->string('bandwidth_limit'); // MikroTik format: "5M/5M"
    $table->timestamps();
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_plans');
    }
};
