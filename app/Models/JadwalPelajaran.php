<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id', 'kelas_id', 'mata_pelajaran_id',
        'tahun_ajaran_id', 'hari', 'jam_mulai', 'jam_selesai',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
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

    // Relasi ke jurnal — dipakai untuk withExists() di DetailJurnalGuru
    public function jurnalHariIni()
    {
        return $this->hasMany(JurnalGuru::class, 'guru_id', 'guru_id')
            ->whereColumn('jurnal_gurus.kelas_id', 'jadwal_pelajarans.kelas_id')
            ->whereColumn('jurnal_gurus.mata_pelajaran_id', 'jadwal_pelajarans.mata_pelajaran_id');
    }

    public function getHariLabelAttribute(): string
    {
        return match($this->hari) {
            'senin'  => 'Senin',  'selasa' => 'Selasa',
            'rabu'   => 'Rabu',   'kamis'  => 'Kamis',
            'jumat'  => 'Jumat',  'sabtu'  => 'Sabtu',
            default  => '-',
        };
    }
}