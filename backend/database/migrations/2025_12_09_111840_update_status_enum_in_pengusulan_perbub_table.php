<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update enum status to include new statuses
        DB::statement("ALTER TABLE pengusulan_perbub MODIFY COLUMN status ENUM(
            'draft',
            'diajukan',
            'usulan',
            'pengkajian_usulan',
            'pengkajian_draf',
            'harmonisasi_kemenkumham',
            'fasilitasi_biro_hukum_provinsi',
            'diterima',
            'selesai',
            'ditolak',
            'revisi'
        ) DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        DB::statement("ALTER TABLE pengusulan_perbub MODIFY COLUMN status ENUM(
            'draft',
            'diajukan',
            'diterima',
            'ditolak',
            'revisi'
        ) DEFAULT 'draft'");
    }
};
