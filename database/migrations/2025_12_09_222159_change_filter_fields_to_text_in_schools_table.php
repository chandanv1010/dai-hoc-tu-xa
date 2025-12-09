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
            // Đổi cột từ JSON sang TEXT để lưu string đơn giản
            if (Schema::hasColumn('schools', 'graduation_system')) {
                $table->text('graduation_system')->nullable()->change();
            }
            if (Schema::hasColumn('schools', 'training_majors')) {
                $table->text('training_majors')->nullable()->change();
            }
            if (Schema::hasColumn('schools', 'exam_location')) {
                $table->text('exam_location')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Đổi lại về JSON nếu cần rollback
            if (Schema::hasColumn('schools', 'graduation_system')) {
                $table->json('graduation_system')->nullable()->change();
            }
            if (Schema::hasColumn('schools', 'training_majors')) {
                $table->json('training_majors')->nullable()->change();
            }
            if (Schema::hasColumn('schools', 'exam_location')) {
                $table->json('exam_location')->nullable()->change();
            }
        });
    }
};
