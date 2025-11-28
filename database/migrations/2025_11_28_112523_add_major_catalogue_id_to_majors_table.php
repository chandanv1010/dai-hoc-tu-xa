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
            $table->unsignedBigInteger('major_catalogue_id')->nullable()->after('user_id');
            $table->foreign('major_catalogue_id')->references('id')->on('major_catalogues')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('majors', function (Blueprint $table) {
            $table->dropForeign(['major_catalogue_id']);
            $table->dropColumn('major_catalogue_id');
        });
    }
};
