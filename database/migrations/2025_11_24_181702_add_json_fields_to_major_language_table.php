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
            if (!Schema::hasColumn('major_language', 'overview_items')) {
                $table->json('overview_items')->nullable()->comment('Danh sách items Toàn Cảnh Ngành')->after('what_learn_description');
            }
            if (!Schema::hasColumn('major_language', 'suitable_items')) {
                $table->json('suitable_items')->nullable()->comment('Danh sách Ai phù hợp')->after('overview_items');
            }
            if (!Schema::hasColumn('major_language', 'advantage_items')) {
                $table->json('advantage_items')->nullable()->comment('Danh sách Ưu điểm')->after('suitable_items');
            }
            if (!Schema::hasColumn('major_language', 'what_learn_categories')) {
                $table->json('what_learn_categories')->nullable()->comment('Danh sách mục và bài Bạn sẽ học được gì')->after('advantage_items');
            }
            if (!Schema::hasColumn('major_language', 'career_tags')) {
                $table->json('career_tags')->nullable()->comment('Danh sách tag Cơ hội việc làm')->after('what_learn_categories');
            }
            if (!Schema::hasColumn('major_language', 'career_jobs')) {
                $table->json('career_jobs')->nullable()->comment('Danh sách nghề Cơ hội việc làm')->after('career_tags');
            }
            if (!Schema::hasColumn('major_language', 'degree_value_items')) {
                $table->json('degree_value_items')->nullable()->comment('Danh sách giá trị văn bằng')->after('career_jobs');
            }
            if (!Schema::hasColumn('major_language', 'student_feedbacks')) {
                $table->json('student_feedbacks')->nullable()->comment('Danh sách cảm nhận học viên')->after('degree_value_items');
            }
            if (!Schema::hasColumn('major_language', 'event_post_ids')) {
                $table->json('event_post_ids')->nullable()->comment('Danh sách ID bài viết sự kiện')->after('student_feedbacks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('major_language', function (Blueprint $table) {
            $columns = [
                'overview_items',
                'suitable_items',
                'advantage_items',
                'what_learn_categories',
                'career_tags',
                'career_jobs',
                'degree_value_items',
                'student_feedbacks',
                'event_post_ids'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('major_language', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
