<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $fillable = [
        'siswa_id', 'kelas_id', 'mata_pelajaran_id',
        'tahun_ajaran_id', 'jenis', 'nilai',
        'keterangan', 'dicatat_oleh',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function getJenisLabelAttribute(): string
    {
        return match($this->jenis) {
            'harian' => 'Harian',
            'tugas'  => 'Tugas',
            'uts'    => 'UTS',
            'uas'    => 'UAS',
            default  => '-',
        };
    }
}