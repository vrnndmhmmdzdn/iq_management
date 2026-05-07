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
        Schema::create('pembayaran_spps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('periode'); // format: 2025-01
            $table->bigInteger('nominal');
            $table->string('bukti_pembayaran')->nullable();
            $table->text('catatan_ortu')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->enum('status', ['belum_bayar','menunggu', 'dikonfirmasi', 'ditolak'])->default('belum_bayar');
            $table->boolean('is_tagihan')->default(false);
            $table->foreignId('dikonfirmasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dikonfirmasi_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_spps');
    }
};
