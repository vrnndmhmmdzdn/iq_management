<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();

            // Satu guru tidak bisa ngajar 2 kelas berbeda di hari + jam yang sama
            $table->unique(
                ['guru_id', 'hari', 'jam_mulai', 'tahun_ajaran_id'],
                'jadwal_unik_per_guru'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajarans');
    }
};