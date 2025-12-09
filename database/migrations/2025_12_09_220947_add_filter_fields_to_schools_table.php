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
        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'graduation_system')) {
                $table->json('graduation_system')->nullable()->comment('Hệ Tốt Nghiệp (JSON array)');
            }
            if (!Schema::hasColumn('schools', 'training_majors')) {
                $table->json('training_majors')->nullable()->comment('Ngành Đào Tạo (JSON array)');
            }
            if (!Schema::hasColumn('schools', 'exam_location')) {
                $table->json('exam_location')->nullable()->comment('Địa Điểm Thi (JSON array)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'graduation_system')) {
                $table->dropColumn('graduation_system');
            }
            if (Schema::hasColumn('schools', 'training_majors')) {
                $table->dropColumn('training_majors');
            }
            if (Schema::hasColumn('schools', 'exam_location')) {
                $table->dropColumn('exam_location');
            }
        });
    }
};
