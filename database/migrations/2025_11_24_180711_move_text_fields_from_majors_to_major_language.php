<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm các trường text vào major_language
        Schema::table('major_language', function (Blueprint $table) {
            if (!Schema::hasColumn('major_language', 'overview_title')) {
                $table->string('overview_title')->nullable()->comment('Tiêu đề Toàn Cảnh Ngành')->after('training_duration');
            }
            if (!Schema::hasColumn('major_language', 'overview_content')) {
                $table->longText('overview_content')->nullable()->comment('Nội dung Toàn Cảnh Ngành')->after('overview_title');
            }
            if (!Schema::hasColumn('major_language', 'career_description')) {
                $table->string('career_description')->nullable()->comment('Mô tả cơ hội việc làm')->after('overview_content');
            }
            if (!Schema::hasColumn('major_language', 'choose_school_content')) {
                $table->text('choose_school_content')->nullable()->comment('Nội dung chọn trường')->after('career_description');
            }
            if (!Schema::hasColumn('major_language', 'choose_school_note')) {
                $table->string('choose_school_note')->nullable()->comment('Lưu ý chọn trường')->after('choose_school_content');
            }
            if (!Schema::hasColumn('major_language', 'degree_value_title')) {
                $table->string('degree_value_title')->nullable()->comment('Tiêu đề giá trị văn bằng')->after('choose_school_note');
            }
            if (!Schema::hasColumn('major_language', 'degree_value_description')) {
                $table->string('degree_value_description')->nullable()->comment('Mô tả giá trị văn bằng')->after('degree_value_title');
            }
            if (!Schema::hasColumn('major_language', 'student_feedback_description')) {
                $table->text('student_feedback_description')->nullable()->comment('Mô tả cảm nhận học viên')->after('degree_value_description');
            }
            if (!Schema::hasColumn('major_language', 'what_learn_description')) {
                $table->text('what_learn_description')->nullable()->comment('Mô tả bạn sẽ học được gì')->after('student_feedback_description');
            }
        });

        // Di chuyển dữ liệu từ majors sang major_language (nếu có)
        // Lưu ý: Chỉ di chuyển nếu có dữ liệu, và cần map với language_id
        // Ở đây chúng ta sẽ giữ lại các trường trong majors nhưng không dùng nữa
        // Hoặc có thể xóa sau khi đã migrate dữ liệu
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('major_language', function (Blueprint $table) {
            $columns = [
                'overview_title',
                'overview_content',
                'career_description',
                'choose_school_content',
                'choose_school_note',
                'degree_value_title',
                'degree_value_description',
                'student_feedback_description',
                'what_learn_description'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('major_language', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
