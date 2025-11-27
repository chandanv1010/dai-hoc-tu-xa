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
        Schema::table('majors', function (Blueprint $table) {
            // Xóa các cột từ overview_title đến show_what_learn
            $columnsToDrop = [
                'overview_title',
                'overview_content',
                'overview_image',
                'show_overview',
                'career_description',
                'show_career',
                'choose_school_content',
                'choose_school_image',
                'choose_school_note',
                'show_choose_school',
                'degree_value_image',
                'degree_value_title',
                'degree_value_description',
                'show_degree_value',
                'student_feedback_description',
                'show_student_feedback',
                'show_suitable',
                'show_advantage',
                'what_learn_description',
                'show_what_learn',
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('majors', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Thêm các cột is_show_* sau study_path_file
            if (!Schema::hasColumn('majors', 'is_show_feature')) {
                $table->tinyInteger('is_show_feature')->default(2)->comment('1: Ẩn, 2: Hiện - Danh sách tính năng')->after('study_path_file');
            }
            if (!Schema::hasColumn('majors', 'is_show_overview')) {
                $table->tinyInteger('is_show_overview')->default(2)->comment('1: Ẩn, 2: Hiện - Toàn Cảnh Ngành')->after('is_show_feature');
            }
            if (!Schema::hasColumn('majors', 'is_show_who')) {
                $table->tinyInteger('is_show_who')->default(2)->comment('1: Ẩn, 2: Hiện - Ai phù hợp')->after('is_show_overview');
            }
            if (!Schema::hasColumn('majors', 'is_show_priority')) {
                $table->tinyInteger('is_show_priority')->default(2)->comment('1: Ẩn, 2: Hiện - Ưu điểm')->after('is_show_who');
            }
            if (!Schema::hasColumn('majors', 'is_show_learn')) {
                $table->tinyInteger('is_show_learn')->default(2)->comment('1: Ẩn, 2: Hiện - Bạn sẽ học được gì')->after('is_show_priority');
            }
            if (!Schema::hasColumn('majors', 'is_show_chance')) {
                $table->tinyInteger('is_show_chance')->default(2)->comment('1: Ẩn, 2: Hiện - Cơ hội việc làm')->after('is_show_learn');
            }
            if (!Schema::hasColumn('majors', 'is_show_school')) {
                $table->tinyInteger('is_show_school')->default(2)->comment('1: Ẩn, 2: Hiện - Chọn trường')->after('is_show_chance');
            }
            if (!Schema::hasColumn('majors', 'is_show_value')) {
                $table->tinyInteger('is_show_value')->default(2)->comment('1: Ẩn, 2: Hiện - Giá trị văn bằng')->after('is_show_school');
            }
            if (!Schema::hasColumn('majors', 'is_show_feedback')) {
                $table->tinyInteger('is_show_feedback')->default(2)->comment('1: Ẩn, 2: Hiện - Cảm nhận học viên')->after('is_show_value');
            }
            if (!Schema::hasColumn('majors', 'is_show_event')) {
                $table->tinyInteger('is_show_event')->default(2)->comment('1: Ẩn, 2: Hiện - Sự kiện')->after('is_show_feedback');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('majors', function (Blueprint $table) {
            // Xóa các cột is_show_*
            $isShowColumns = [
                'is_show_feature',
                'is_show_overview',
                'is_show_who',
                'is_show_priority',
                'is_show_learn',
                'is_show_chance',
                'is_show_school',
                'is_show_value',
                'is_show_feedback',
                'is_show_event',
            ];
            
            foreach ($isShowColumns as $column) {
                if (Schema::hasColumn('majors', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
