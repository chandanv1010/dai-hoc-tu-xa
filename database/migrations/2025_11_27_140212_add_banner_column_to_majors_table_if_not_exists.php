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
        // Kiểm tra xem cột banner đã tồn tại chưa
        if (!Schema::hasColumn('majors', 'banner')) {
            Schema::table('majors', function (Blueprint $table) {
                $table->string('banner')->nullable()->after('subtitle')->comment('Banner ngành học');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Chỉ xóa cột nếu nó tồn tại
        if (Schema::hasColumn('majors', 'banner')) {
            Schema::table('majors', function (Blueprint $table) {
                $table->dropColumn('banner');
            });
        }
    }
};
