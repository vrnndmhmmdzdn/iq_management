<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class JurnalGuru extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'guru_id', 'kelas_id', 'mata_pelajaran_id', 'tahun_ajaran_id',
        'tanggal', 'pertemuan_ke', 'materi', 'kompetensi_dasar',
        'indikator_pencapaian', 'deskripsi_kegiatan', 'metode_pembelajaran',
        'media_pembelajaran', 'jam_mulai', 'jam_selesai',
        'jumlah_hadir', 'jumlah_tidak_hadir', 'capaian',
        'penilaian_dilakukan', 'jenis_penilaian', 'tindak_lanjut',
        'catatan', 'status', 'submitted_at',
    ];

    protected $casts = [
        'tanggal'             => 'date',
        'penilaian_dilakukan' => 'boolean',
        'submitted_at'        => 'datetime',
    ];

    // guru_id sekarang relasi ke tabel gurus
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

    public function lampirans()
    {
        return $this->hasMany(JurnalLampiran::class);
    }

    public function komentars()
    {
        return $this->hasMany(JurnalKomentar::class);
    }

    public function getMetodeLabelAttribute(): string
    {
        return match($this->metode_pembelajaran) {
            'ceramah'     => 'Ceramah',
            'diskusi'     => 'Diskusi',
            'praktik'     => 'Praktik',
            'demonstrasi' => 'Demonstrasi',
            'tanya_jawab' => 'Tanya Jawab',
            'penugasan'   => 'Penugasan',
            'project'     => 'Project Based Learning',
            'lainnya'     => 'Lainnya',
            default       => '-',
        };
    }

    public function getCapaianLabelAttribute(): string
    {
        return match($this->capaian) {
            'tercapai' => 'Tercapai',
            'sebagian' => 'Sebagian Tercapai',
            'belum'    => 'Belum Tercapai',
            default    => '-',
        };
    }

    public function getDurasiAttribute(): string
    {
        if (! $this->jam_mulai || ! $this->jam_selesai) return '-';
        $mulai   = \Carbon\Carbon::parse($this->jam_mulai);
        $selesai = \Carbon\Carbon::parse($this->jam_selesai);
        $menit   = $mulai->diffInMinutes($selesai);
        return "{$menit} menit";
    }
}