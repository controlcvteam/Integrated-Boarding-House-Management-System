<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->time('payment_time')->nullable()->after('payment_date');
        });

        // Backfill existing payments with time from created_at
        $timeExpr = DB::connection()->getDriverName() === 'pgsql' ? 'CAST(created_at AS time)' : 'TIME(created_at)';
        DB::statement("UPDATE payments SET payment_time = {$timeExpr} WHERE payment_time IS NULL AND created_at IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_time');
        });
    }
};
