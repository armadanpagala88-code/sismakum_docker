<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_perbub', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengusulan_id')->constrained('pengusulan_perbub')->onDelete('cascade');
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('tipe_dokumen'); // draft, lampiran, revisi
            $table->integer('ukuran_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_perbub');
    }
};

