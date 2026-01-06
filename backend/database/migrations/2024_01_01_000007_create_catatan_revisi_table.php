<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_revisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengusulan_id')->constrained('pengusulan_perbub')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('tipe', ['revisi', 'tolak', 'catatan'])->default('catatan');
            $table->text('catatan');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_revisi');
    }
};

