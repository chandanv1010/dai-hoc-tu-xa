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
            // Check if columns exist before adding
            if (!Schema::hasColumn('major_language', 'content')) {
                $table->longText('content')->nullable()->comment('Tổng quan chương trình')->after('description');
            }
            if (!Schema::hasColumn('major_language', 'training_system')) {
                $table->string('training_system')->nullable()->comment('Hệ đào tạo')->after('content');
            }
            if (!Schema::hasColumn('major_language', 'study_method')) {
                $table->string('study_method')->nullable()->comment('Hình thức học')->after('training_system');
            }
            if (!Schema::hasColumn('major_language', 'admission_method')) {
                $table->string('admission_method')->nullable()->comment('Hình thức tuyển')->after('study_method');
            }
            if (!Schema::hasColumn('major_language', 'enrollment_quota')) {
                $table->string('enrollment_quota')->nullable()->comment('Chỉ tiêu tuyển')->after('admission_method');
            }
            if (!Schema::hasColumn('major_language', 'enrollment_period')) {
                $table->string('enrollment_period')->nullable()->comment('Thời gian tuyển')->after('enrollment_quota');
            }
            if (!Schema::hasColumn('major_language', 'admission_type')) {
                $table->string('admission_type')->nullable()->comment('Loại tuyển sinh')->after('enrollment_period');
            }
            if (!Schema::hasColumn('major_language', 'degree_type')) {
                $table->string('degree_type')->nullable()->comment('Loại văn bằng')->after('admission_type');
            }
            if (!Schema::hasColumn('major_language', 'training_duration')) {
                $table->string('training_duration')->nullable()->comment('Thời gian đào tạo')->after('degree_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('major_language', function (Blueprint $table) {
            $table->dropColumn([
                'content',
                'training_system',
                'study_method',
                'admission_method',
                'enrollment_quota',
                'enrollment_period',
                'admission_type',
                'degree_type',
                'training_duration'
            ]);
        });
    }
};
