<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengusulan_perbub', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat');
            $table->date('tanggal_surat');
            $table->foreignId('dinas_id')->constrained('users')->onDelete('cascade');
            $table->string('judul_perbub');
            $table->text('latar_belakang');
            $table->text('maksud_dan_tujuan');
            $table->text('ruang_lingkup');
            $table->enum('status', ['draft', 'diajukan', 'diterima', 'ditolak', 'revisi'])->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengusulan_perbub');
    }
};

