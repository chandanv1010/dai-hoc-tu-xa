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
            $table->string('training_system')->nullable()->comment('Hệ đào tạo');
            $table->string('study_method')->nullable()->comment('Hình thức học');
            $table->string('admission_method')->nullable()->comment('Hình thức tuyển');
            $table->string('enrollment_quota')->nullable()->comment('Chỉ tiêu tuyển');
            $table->string('enrollment_period')->nullable()->comment('Thời gian tuyển');
            $table->string('admission_type')->nullable()->comment('Loại tuyển sinh');
            $table->string('degree_type')->nullable()->comment('Loại văn bằng');
            $table->string('training_duration')->nullable()->comment('Thời gian đào tạo');
            $table->longText('content')->nullable()->comment('Tổng quan chương trình');
        });

        Schema::table('majors', function (Blueprint $table) {
            $table->string('study_path_file')->nullable()->comment('Lộ trình học (file)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('major_language', function (Blueprint $table) {
            $table->dropColumn([
                'training_system',
                'study_method',
                'admission_method',
                'enrollment_quota',
                'enrollment_period',
                'admission_type',
                'degree_type',
                'training_duration',
                'content'
            ]);
        });

        Schema::table('majors', function (Blueprint $table) {
            $table->dropColumn('study_path_file');
        });
    }
};
