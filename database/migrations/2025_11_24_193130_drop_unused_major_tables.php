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
        // Drop các bảng không cần thiết vì dữ liệu đã được lưu dưới dạng JSON trong major_language
        // Tắt foreign key checks tạm thời để tránh lỗi constraint
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tablesToDrop = [
            'major_what_learn_items', // Xóa trước vì có foreign key
            'major_features',
            'major_admission_targets',
            'major_reception_places',
            'major_overview_items',
            'major_suitable_items',
            'major_advantage_items',
            'major_what_learn_categories',
            'major_career_tags',
            'major_career_jobs',
            'major_degree_value_items',
            'major_student_feedbacks',
            'major_event_posts',
        ];

        foreach ($tablesToDrop as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
            }
        }
        
        // Bật lại foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần reverse vì các bảng này không còn được sử dụng
    }
};
