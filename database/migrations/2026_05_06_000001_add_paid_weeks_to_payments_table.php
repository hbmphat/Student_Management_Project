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
        if (!Schema::hasColumn('payments', 'paid_weeks')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->integer('paid_weeks')->after('receipt_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('payments', 'paid_weeks')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('paid_weeks');
            });
        }
    }
};