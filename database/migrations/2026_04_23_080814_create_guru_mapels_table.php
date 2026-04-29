<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ganti 'table' menjadi 'create'
        Schema::create('guru_mapels', function (Blueprint $table) {
            $table->id(); // Tambahkan id jika belum ada
            
            // Definisikan kolom sekaligus foreign key-nya
            $table->foreignId('guru_id')
                ->nullable()
                ->constrained('gurus')
                ->cascadeOnDelete();

            $table->foreignId('mata_pelajaran_id')
                ->constrained('mata_pelajarans')
                ->cascadeOnDelete();

            // Mencegah duplikasi: satu guru tidak bisa pegang mapel yang sama dua kali
            $table->unique(['guru_id', 'mata_pelajaran_id']);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_mapels');
    }
};
