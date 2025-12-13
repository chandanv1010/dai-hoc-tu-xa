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
        Schema::table('major_language', function (Blueprint $table) {
            if (!Schema::hasColumn('major_language', 'total_credits')) {
                $table->string('total_credits')->nullable()->comment('Tổng số tín chỉ tích lũy')->after('training_duration');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('major_language', function (Blueprint $table) {
            if (Schema::hasColumn('major_language', 'total_credits')) {
                $table->dropColumn('total_credits');
            }
        });
    }
};