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
        // Xóa dữ liệu trong bảng majors và major_language để reseed với cấu trúc mới
        \Illuminate\Support\Facades\DB::table('routers')->where('controllers', 'App\Http\Controllers\Frontend\MajorController')->delete();
        // Xóa major_language trước vì có foreign key constraint
        \Illuminate\Support\Facades\DB::table('major_language')->delete();
        // Sau đó xóa majors
        \Illuminate\Support\Facades\DB::table('majors')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần rollback
    }
};
