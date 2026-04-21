<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class PembayaranSpp extends Model
{
     use HasFactory;

    protected $fillable = [
        'siswa_id', 'user_id', 'periode', 'nominal',
        'bukti_pembayaran', 'catatan_ortu', 'catatan_admin',
        'status', 'dikonfirmasi_oleh', 'dikonfirmasi_at',
    ];

    protected $casts = [
        'dikonfirmasi_at' => 'datetime',
    ];

    const STATUS_MENUNGGU     = 'menunggu';
    const STATUS_DIKONFIRMASI = 'dikonfirmasi';
    const STATUS_DITOLAK      = 'ditolak';

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dikonfirmasiOleh()
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

    public function getPeriodeLabelAttribute(): string
    {
        return Carbon::createFromFormat('Y-m', $this->periode)
            ->locale('id')
            ->translatedFormat('F Y');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'menunggu'     => 'Menunggu Konfirmasi',
            'dikonfirmasi' => 'Lunas',
            'ditolak'      => 'Ditolak',
            default        => '-',
        };
    }

}
