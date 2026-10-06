<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add the balance column
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('balance', 12, 2)->default(0)->after('total_amount');
            $table->index('balance');
        });

        // Backfill: for existing invoices, balance = total_amount unless already paid
        DB::table('invoices')->update([
            'balance' => DB::raw("
                CASE
                    WHEN status = 'paid' THEN 0
                    ELSE total_amount
                END
            "),
        ]);
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['balance']);
            $table->dropColumn('balance');
        });
    }
};