<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nis', 'nisn', 'nama_lengkap', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'alamat', 'foto',
        'status', 'kelas_id', 'tahun_ajaran_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function orangTua()
    {
        return $this->hasMany(OrangTua::class);
    }

    public function pembayaranSpp()
    {
        return $this->hasMany(PembayaranSpp::class);
    }

    public function isSppLunas(): bool
    {
        $bulanIni = now()->format('Y-m');
        return $this->pembayaranSpp()
            ->where('periode', $bulanIni)
            ->where('status', 'dikonfirmasi')
            ->exists();
    }
}
