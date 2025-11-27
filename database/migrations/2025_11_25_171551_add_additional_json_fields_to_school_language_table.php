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
        Schema::table('school_language', function (Blueprint $table) {
            // Thêm các JSON columns mới
            if (!Schema::hasColumn('school_language', 'suitable')) {
                $table->json('suitable')->nullable()->comment('Phù hợp {name, description, items: [{image, name, description}]}')->after('advantage');
            }
            if (!Schema::hasColumn('school_language', 'majors')) {
                $table->json('majors')->nullable()->comment('Chọn ngành (array of major objects)')->after('suitable');
            }
            if (!Schema::hasColumn('school_language', 'study_method')) {
                $table->json('study_method')->nullable()->comment('Hình thức học {name, description, image, content, items: [{image, name, description}]}')->after('majors');
            }
            if (!Schema::hasColumn('school_language', 'feedback')) {
                $table->json('feedback')->nullable()->comment('Cảm nhận học viên {description, items: [...]}')->after('study_method');
            }
            if (!Schema::hasColumn('school_language', 'event')) {
                $table->json('event')->nullable()->comment('Sự kiện (number[])')->after('feedback');
            }
            if (!Schema::hasColumn('school_language', 'value')) {
                $table->json('value')->nullable()->comment('Giá trị văn bằng {name, image, description, items: [...]}')->after('event');
            }
        });

        Schema::table('schools', function (Blueprint $table) {
            // Thêm các is_show flags mới
            if (!Schema::hasColumn('schools', 'is_show_suitable')) {
                $table->tinyInteger('is_show_suitable')->default(2)->comment('1: Ẩn, 2: Hiện - Phù hợp')->after('is_show_advantage');
            }
            if (!Schema::hasColumn('schools', 'is_show_majors')) {
                $table->tinyInteger('is_show_majors')->default(2)->comment('1: Ẩn, 2: Hiện - Chọn ngành')->after('is_show_suitable');
            }
            if (!Schema::hasColumn('schools', 'is_show_study_method')) {
                $table->tinyInteger('is_show_study_method')->default(2)->comment('1: Ẩn, 2: Hiện - Hình thức học')->after('is_show_majors');
            }
            if (!Schema::hasColumn('schools', 'is_show_feedback')) {
                $table->tinyInteger('is_show_feedback')->default(2)->comment('1: Ẩn, 2: Hiện - Cảm nhận học viên')->after('is_show_study_method');
            }
            if (!Schema::hasColumn('schools', 'is_show_event')) {
                $table->tinyInteger('is_show_event')->default(2)->comment('1: Ẩn, 2: Hiện - Sự kiện')->after('is_show_feedback');
            }
            if (!Schema::hasColumn('schools', 'is_show_value')) {
                $table->tinyInteger('is_show_value')->default(2)->comment('1: Ẩn, 2: Hiện - Giá trị văn bằng')->after('is_show_event');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_language', function (Blueprint $table) {
            $columns = ['suitable', 'majors', 'study_method', 'feedback', 'event', 'value'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('school_language', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('schools', function (Blueprint $table) {
            $columns = ['is_show_suitable', 'is_show_majors', 'is_show_study_method', 'is_show_feedback', 'is_show_event', 'is_show_value'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('schools', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
