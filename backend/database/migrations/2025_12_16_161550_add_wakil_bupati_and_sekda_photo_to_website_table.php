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
        Schema::table('website', function (Blueprint $table) {
            $table->string('wakil_bupati_photo')->nullable()->after('bupati_photo');
            $table->string('sekda_photo')->nullable()->after('wakil_bupati_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website', function (Blueprint $table) {
            $table->dropColumn(['wakil_bupati_photo', 'sekda_photo']);
        });
    }
};
