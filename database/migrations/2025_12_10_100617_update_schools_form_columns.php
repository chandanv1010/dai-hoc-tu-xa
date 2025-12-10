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
            // Xóa các cột cũ
            if (Schema::hasColumn('schools', 'form_json')) {
                $table->dropColumn('form_json');
            }
            if (Schema::hasColumn('schools', 'form_tai_lo_trinh_json')) {
                $table->dropColumn('form_tai_lo_trinh_json');
            }
            if (Schema::hasColumn('schools', 'form_tu_van_mien_phi_json')) {
                $table->dropColumn('form_tu_van_mien_phi_json');
            }
            
            // Thêm 3 cột mới
            $table->json('form_tai_lo_trinh_hoc')->nullable()->after('exam_location');
            $table->json('form_tu_van_mien_phi')->nullable()->after('form_tai_lo_trinh_hoc');
            $table->json('form_hoc_thu')->nullable()->after('form_tu_van_mien_phi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Xóa các cột mới
            if (Schema::hasColumn('schools', 'form_hoc_thu')) {
                $table->dropColumn('form_hoc_thu');
            }
            if (Schema::hasColumn('schools', 'form_tu_van_mien_phi')) {
                $table->dropColumn('form_tu_van_mien_phi');
            }
            if (Schema::hasColumn('schools', 'form_tai_lo_trinh_hoc')) {
                $table->dropColumn('form_tai_lo_trinh_hoc');
            }
            
            // Khôi phục các cột cũ (nếu cần)
            $table->json('form_json')->nullable();
            $table->json('form_tai_lo_trinh_json')->nullable();
            $table->json('form_tu_van_mien_phi_json')->nullable();
        });
    }
};
