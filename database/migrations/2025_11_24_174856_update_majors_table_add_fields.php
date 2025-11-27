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
            if (!Schema::hasColumn('majors', 'study_path_file')) {
                $table->string('study_path_file')->nullable()->comment('File lộ trình học')->after('user_id');
            }
            
            // Toàn Cảnh Ngành
            if (!Schema::hasColumn('majors', 'overview_title')) {
                $table->string('overview_title')->nullable()->comment('Tiêu đề Toàn Cảnh Ngành')->after('study_path_file');
            }
            if (!Schema::hasColumn('majors', 'overview_content')) {
                $table->longText('overview_content')->nullable()->comment('Nội dung Toàn Cảnh Ngành');
            }
            if (!Schema::hasColumn('majors', 'overview_image')) {
                $table->string('overview_image')->nullable()->comment('Ảnh Toàn Cảnh Ngành');
            }
            if (!Schema::hasColumn('majors', 'show_overview')) {
                $table->tinyInteger('show_overview')->default(2)->comment('1: Ẩn, 2: Hiện');
            }
            
            // Cơ hội việc làm
            if (!Schema::hasColumn('majors', 'career_description')) {
                $table->string('career_description')->nullable()->comment('Mô tả cơ hội việc làm');
            }
            if (!Schema::hasColumn('majors', 'show_career')) {
                $table->tinyInteger('show_career')->default(2)->comment('1: Ẩn, 2: Hiện');
            }
            
            // Chọn trường
            if (!Schema::hasColumn('majors', 'choose_school_content')) {
                $table->text('choose_school_content')->nullable()->comment('Nội dung chọn trường');
            }
            if (!Schema::hasColumn('majors', 'choose_school_image')) {
                $table->string('choose_school_image')->nullable()->comment('Ảnh chọn trường');
            }
            if (!Schema::hasColumn('majors', 'choose_school_note')) {
                $table->string('choose_school_note')->nullable()->comment('Lưu ý chọn trường');
            }
            if (!Schema::hasColumn('majors', 'show_choose_school')) {
                $table->tinyInteger('show_choose_school')->default(2)->comment('1: Ẩn, 2: Hiện');
            }
            
            // Giá trị văn bằng
            if (!Schema::hasColumn('majors', 'degree_value_image')) {
                $table->string('degree_value_image')->nullable()->comment('Ảnh giá trị văn bằng');
            }
            if (!Schema::hasColumn('majors', 'degree_value_title')) {
                $table->string('degree_value_title')->nullable()->comment('Tiêu đề giá trị văn bằng');
            }
            if (!Schema::hasColumn('majors', 'degree_value_description')) {
                $table->string('degree_value_description')->nullable()->comment('Mô tả giá trị văn bằng');
            }
            if (!Schema::hasColumn('majors', 'show_degree_value')) {
                $table->tinyInteger('show_degree_value')->default(2)->comment('1: Ẩn, 2: Hiện');
            }
            
            // Cảm nhận học viên
            if (!Schema::hasColumn('majors', 'student_feedback_description')) {
                $table->text('student_feedback_description')->nullable()->comment('Mô tả cảm nhận học viên');
            }
            if (!Schema::hasColumn('majors', 'show_student_feedback')) {
                $table->tinyInteger('show_student_feedback')->default(2)->comment('1: Ẩn, 2: Hiện');
            }
            
            // Ai phù hợp
            if (!Schema::hasColumn('majors', 'show_suitable')) {
                $table->tinyInteger('show_suitable')->default(2)->comment('1: Ẩn, 2: Hiện');
            }
            
            // Ưu điểm
            if (!Schema::hasColumn('majors', 'show_advantage')) {
                $table->tinyInteger('show_advantage')->default(2)->comment('1: Ẩn, 2: Hiện');
            }
            
            // Bạn sẽ học được gì
            if (!Schema::hasColumn('majors', 'what_learn_description')) {
                $table->text('what_learn_description')->nullable()->comment('Mô tả bạn sẽ học được gì');
            }
            if (!Schema::hasColumn('majors', 'show_what_learn')) {
                $table->tinyInteger('show_what_learn')->default(2)->comment('1: Ẩn, 2: Hiện');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('majors', function (Blueprint $table) {
            $columns = [
                'study_path_file',
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
                'show_what_learn'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('majors', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
