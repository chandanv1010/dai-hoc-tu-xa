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
            // Add primary key
            if (!Schema::hasColumn('major_language', 'id')) {
                $table->id()->first();
            }
            
            // Add JSON columns for each block
            if (!Schema::hasColumn('major_language', 'features')) {
                $table->json('features')->nullable()->comment('Danh sách tính năng')->after('meta_description');
            }
            if (!Schema::hasColumn('major_language', 'admission_targets')) {
                $table->json('admission_targets')->nullable()->comment('Đối tượng tuyển sinh')->after('features');
            }
            if (!Schema::hasColumn('major_language', 'reception_places')) {
                $table->json('reception_places')->nullable()->comment('Nơi tiếp nhận hồ sơ')->after('admission_targets');
            }
            if (!Schema::hasColumn('major_language', 'overview')) {
                $table->json('overview')->nullable()->comment('Toàn Cảnh Ngành')->after('reception_places');
            }
            if (!Schema::hasColumn('major_language', 'suitable')) {
                $table->json('suitable')->nullable()->comment('Ai phù hợp')->after('overview');
            }
            if (!Schema::hasColumn('major_language', 'advantage')) {
                $table->json('advantage')->nullable()->comment('Ưu điểm')->after('suitable');
            }
            if (!Schema::hasColumn('major_language', 'what_learn')) {
                $table->json('what_learn')->nullable()->comment('Bạn sẽ học được gì')->after('advantage');
            }
            if (!Schema::hasColumn('major_language', 'career')) {
                $table->json('career')->nullable()->comment('Cơ hội việc làm')->after('what_learn');
            }
            if (!Schema::hasColumn('major_language', 'degree_value')) {
                $table->json('degree_value')->nullable()->comment('Giá trị văn bằng')->after('career');
            }
            if (!Schema::hasColumn('major_language', 'student_feedback')) {
                $table->json('student_feedback')->nullable()->comment('Cảm nhận học viên')->after('degree_value');
            }
            if (!Schema::hasColumn('major_language', 'event_posts')) {
                $table->json('event_posts')->nullable()->comment('Sự kiện')->after('student_feedback');
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
                'id',
                'features',
                'admission_targets',
                'reception_places',
                'overview',
                'suitable',
                'advantage',
                'what_learn',
                'career',
                'degree_value',
                'student_feedback',
                'event_posts'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('major_language', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
