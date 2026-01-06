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
        Schema::table('pengusulan_perbub', function (Blueprint $table) {
            $table->foreignId('kategori_dokumen_id')->nullable()->after('dinas_id')->constrained('kategori_dokumen')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengusulan_perbub', function (Blueprint $table) {
            $table->dropForeign(['kategori_dokumen_id']);
            $table->dropColumn('kategori_dokumen_id');
        });
    }
};
