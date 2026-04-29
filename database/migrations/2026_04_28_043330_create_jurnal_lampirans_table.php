<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_lampirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_guru_id')->constrained('jurnal_gurus')->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('path');
            $table->enum('tipe', ['foto_kegiatan', 'rpp', 'modul', 'lainnya'])->default('foto_kegiatan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_lampirans');
    }
};