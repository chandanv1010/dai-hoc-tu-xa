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
        Schema::create('major_language', function (Blueprint $table) {
            $table->unsignedBigInteger('major_id');
            $table->unsignedBigInteger('language_id');
            $table->foreign('major_id')->references('id')->on('majors')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->string('name')->comment('Tên chuyên ngành');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->string('canonical')->nullable()->comment('Đường dẫn SEO');
            $table->string('meta_title')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('major_language');
    }
};
