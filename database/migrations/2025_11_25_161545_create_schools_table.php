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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable()->comment('Ảnh đại diện');
            $table->tinyInteger('publish')->default(1)->comment('1: Chưa kích hoạt, 2: Đã kích hoạt');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Khối 1: Statistics - Các số liệu
            $table->integer('statistics_majors')->nullable()->comment('Số ngành học');
            $table->integer('statistics_students')->nullable()->comment('Số học viên');
            $table->integer('statistics_courses')->nullable()->comment('Số khóa khai giảng');
            $table->integer('statistics_satisfaction')->nullable()->comment('Tỷ lệ hài lòng (%)');
            $table->integer('statistics_employment')->nullable()->comment('Tỷ lệ có việc làm (%)');
            
            // Show flags
            $table->tinyInteger('is_show_statistics')->default(2)->comment('1: Ẩn, 2: Hiện - Số liệu thống kê');
            $table->tinyInteger('is_show_intro')->default(2)->comment('1: Ẩn, 2: Hiện - Giới thiệu');
            $table->tinyInteger('is_show_announce')->default(2)->comment('1: Ẩn, 2: Hiện - Thông báo tuyển sinh');
            $table->tinyInteger('is_show_advantage')->default(2)->comment('1: Ẩn, 2: Hiện - Ưu điểm');
            
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
