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
        Schema::create('school_language', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('language_id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            
            // Thông tin cơ bản
            $table->string('name')->comment('Tên trường');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->longText('content')->nullable()->comment('Nội dung');
            $table->string('canonical')->nullable()->comment('Đường dẫn SEO');
            $table->string('meta_title')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->text('meta_description')->nullable();
            
            // Khối 2: Giới thiệu - JSON
            $table->json('intro')->nullable()->comment('Giới thiệu {name, description, created, top, percent}');
            
            // Khối 3: Thông báo tuyển sinh - JSON
            $table->json('announce')->nullable()->comment('Thông báo tuyển sinh {description, content, target, type, request, address, value}');
            
            // Khối 4: Ưu điểm - JSON
            $table->json('advantage')->nullable()->comment('Ưu điểm {title, description, items: [{name, description, icon, note}]}');
            
            $table->timestamps();
            
            // Đặt canonical làm unique
            $table->unique('canonical');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_language');
    }
};
