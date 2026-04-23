<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Tugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul', 'deskripsi', 'mata_pelajaran_id',
        'kelas_id', 'created_by', 'tanggal',
        'batas_waktu', 'file_lampiran', 'is_aktif',
    ];

    protected $casts = [
        'tanggal'   => 'date',
        'is_aktif'  => 'boolean',
    ];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeHariIni($query)
    {
        return $query->where('tanggal', today());
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function isTerlambat(): bool
    {
        if (!$this->batas_waktu) return false;
        return now()->gt(Carbon::parse($this->tanggal->format('Y-m-d').' '.$this->batas_waktu));
    }
}