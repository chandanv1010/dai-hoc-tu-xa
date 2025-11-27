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
            // Xóa các cột JSON cũ nếu tồn tại
            $oldJsonColumns = [
                'features',
                'admission_targets',
                'reception_places',
                'overview_items',
                'suitable_items',
                'advantage_items',
                'what_learn_categories',
                'career_tags',
                'career_jobs',
                'degree_value_items',
                'student_feedbacks',
                'event_post_ids',
            ];
            
            foreach ($oldJsonColumns as $column) {
                if (Schema::hasColumn('major_language', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Xóa các cột text cũ nếu tồn tại
            $oldTextColumns = [
                'overview_title',
                'overview_content',
                'career_description',
                'choose_school_content',
                'choose_school_note',
                'degree_value_title',
                'degree_value_description',
                'student_feedback_description',
                'what_learn_description',
            ];
            
            foreach ($oldTextColumns as $column) {
                if (Schema::hasColumn('major_language', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Thêm các cột JSON mới sau meta_description
            if (!Schema::hasColumn('major_language', 'feature')) {
                $table->json('feature')->nullable()->comment('Danh sách tính năng [{name, image}]')->after('meta_description');
            }
            if (!Schema::hasColumn('major_language', 'target')) {
                $table->json('target')->nullable()->comment('Đối tượng tuyển sinh (string[])')->after('feature');
            }
            if (!Schema::hasColumn('major_language', 'address')) {
                $table->json('address')->nullable()->comment('Nơi tiếp nhận hồ sơ [{name, address}]')->after('target');
            }
            if (!Schema::hasColumn('major_language', 'overview')) {
                $table->json('overview')->nullable()->comment('Toàn Cảnh Ngành {name, description, image, items: [...]}')->after('address');
            }
            if (!Schema::hasColumn('major_language', 'who')) {
                $table->json('who')->nullable()->comment('Ai phù hợp [{name, image, description, person}]')->after('overview');
            }
            if (!Schema::hasColumn('major_language', 'priority')) {
                $table->json('priority')->nullable()->comment('Ưu điểm [{name, image, description}]')->after('who');
            }
            if (!Schema::hasColumn('major_language', 'learn')) {
                $table->json('learn')->nullable()->comment('Bạn sẽ học được gì {description, items: [...]}')->after('priority');
            }
            if (!Schema::hasColumn('major_language', 'chance')) {
                $table->json('chance')->nullable()->comment('Cơ hội việc làm {description, tags: [...], job: [...]}')->after('learn');
            }
            if (!Schema::hasColumn('major_language', 'school')) {
                $table->json('school')->nullable()->comment('Chọn trường {description, image, note}')->after('chance');
            }
            if (!Schema::hasColumn('major_language', 'value')) {
                $table->json('value')->nullable()->comment('Giá trị văn bằng {name, image, description, items: [...]}')->after('school');
            }
            if (!Schema::hasColumn('major_language', 'feedback')) {
                $table->json('feedback')->nullable()->comment('Cảm nhận học viên {description, items: [...]}')->after('value');
            }
            if (!Schema::hasColumn('major_language', 'event')) {
                $table->json('event')->nullable()->comment('Sự kiện (number[])')->after('feedback');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('major_language', function (Blueprint $table) {
            // Xóa các cột JSON mới
            $newJsonColumns = [
                'feature',
                'target',
                'address',
                'overview',
                'who',
                'priority',
                'learn',
                'chance',
                'school',
                'value',
                'feedback',
                'event',
            ];
            
            foreach ($newJsonColumns as $column) {
                if (Schema::hasColumn('major_language', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
