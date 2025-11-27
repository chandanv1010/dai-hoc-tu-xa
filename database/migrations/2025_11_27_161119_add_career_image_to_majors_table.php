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
        Schema::table('majors', function (Blueprint $table) {
            if (!Schema::hasColumn('majors', 'career_image')) {
                $table->string('career_image')->nullable()->after('banner')->comment('Ảnh cơ hội việc làm');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('majors', function (Blueprint $table) {
            if (Schema::hasColumn('majors', 'career_image')) {
                $table->dropColumn('career_image');
            }
        });
    }
};
