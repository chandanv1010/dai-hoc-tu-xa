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
            $table->json('form_tai_lo_trinh_json')->nullable()->after('exam_location');
            $table->json('form_tu_van_mien_phi_json')->nullable()->after('form_tai_lo_trinh_json');
            $table->json('form_hoc_thu_json')->nullable()->after('form_tu_van_mien_phi_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('form_tai_lo_trinh_json');
            $table->dropColumn('form_tu_van_mien_phi_json');
            $table->dropColumn('form_hoc_thu_json');
        });
    }
};
