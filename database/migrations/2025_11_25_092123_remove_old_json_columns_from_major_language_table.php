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
            // Xóa các cột JSON cũ đã được thay thế
            $oldColumns = [
                'suitable',        // Thay bằng 'who'
                'advantage',        // Thay bằng 'priority'
                'what_learn',      // Thay bằng 'learn'
                'career',          // Thay bằng 'chance'
                'degree_value',    // Thay bằng 'value'
                'student_feedback', // Thay bằng 'feedback'
                'event_posts',     // Thay bằng 'event'
            ];
            
            foreach ($oldColumns as $column) {
                if (Schema::hasColumn('major_language', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('major_language', function (Blueprint $table) {
            // Khôi phục các cột cũ (nếu cần rollback)
            $table->json('suitable')->nullable();
            $table->json('advantage')->nullable();
            $table->json('what_learn')->nullable();
            $table->json('career')->nullable();
            $table->json('degree_value')->nullable();
            $table->json('student_feedback')->nullable();
            $table->json('event_posts')->nullable();
        });
    }
};
