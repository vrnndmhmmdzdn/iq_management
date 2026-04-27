<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SISWAS
        Schema::table('siswas', function (Blueprint $table) {
            $table->index('kelas_id');
            $table->index('tahun_ajaran_id');
            $table->index('status');
            $table->index('deleted_at');
        });

        // KELAS
        Schema::table('kelas', function (Blueprint $table) {
            $table->index('wali_kelas_id');
            $table->index('tahun_ajaran_id');
        });

        // ORANG_TUAS
        Schema::table('orang_tuas', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('siswa_id');
        });

        // PEMBAYARAN_SPPS
        Schema::table('pembayaran_spps', function (Blueprint $table) {
            $table->index('siswa_id');
            $table->index('status');
            $table->index('periode');
            $table->index('user_id');
        });

        // TUGAS
        Schema::table('tugas', function (Blueprint $table) {
            $table->index('kelas_id');
            $table->index('mata_pelajaran_id');
            $table->index('created_by');
            $table->index('tanggal');
            $table->index('is_aktif');
        });

        // ABSENSIS
        Schema::table('absensis', function (Blueprint $table) {
            $table->index('kelas_id');
            $table->index('tahun_ajaran_id');
            $table->index('tanggal');
            $table->index('status');
            $table->index('dicatat_oleh');
        });

        // NILAIS
        Schema::table('nilais', function (Blueprint $table) {
            $table->index(['kelas_id', 'mata_pelajaran_id', 'jenis']);
            $table->index('siswa_id');
            $table->index('tahun_ajaran_id');
            $table->index('dicatat_oleh');
        });

        // GURU_MAPELS
        Schema::table('guru_mapels', function (Blueprint $table) {
            $table->index('guru_id');
            $table->index('mata_pelajaran_id');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropIndex(['kelas_id']);
            $table->dropIndex(['tahun_ajaran_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->dropIndex(['wali_kelas_id']);
            $table->dropIndex(['tahun_ajaran_id']);
        });

        Schema::table('orang_tuas', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['siswa_id']);
        });

        Schema::table('pembayaran_spps', function (Blueprint $table) {
            $table->dropIndex(['siswa_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['periode']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('tugas', function (Blueprint $table) {
            $table->dropIndex(['kelas_id']);
            $table->dropIndex(['mata_pelajaran_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['is_aktif']);
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndex(['kelas_id']);
            $table->dropIndex(['tahun_ajaran_id']);
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['status']);
            $table->dropIndex(['dicatat_oleh']);
        });

        Schema::table('nilais', function (Blueprint $table) {
            $table->dropIndex(['kelas_id', 'mata_pelajaran_id', 'jenis']);
            $table->dropIndex(['siswa_id']);
            $table->dropIndex(['tahun_ajaran_id']);
            $table->dropIndex(['dicatat_oleh']);
        });

        Schema::table('guru_mapels', function (Blueprint $table) {
            $table->dropIndex(['guru_id']);
            $table->dropIndex(['mata_pelajaran_id']);
        });
    }
};