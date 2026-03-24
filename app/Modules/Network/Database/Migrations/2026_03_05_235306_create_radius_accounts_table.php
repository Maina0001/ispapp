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
    Schema::create('radius_accounts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tenant_id')->constrained(); // For your multi-tenancy
        $table->foreignId('customer_id')->nullable()->constrained();
        
        // Standard RADIUS Fields
        $table->string('username')->index();
        $table->string('attribute')->default('Cleartext-Password');
        $table->string('op', 2)->default(':=');
        $table->string('value');
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radius_accounts');
    }
};