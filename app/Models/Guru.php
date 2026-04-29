<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'nip', 'nama_lengkap', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'alamat',
        'no_hp', 'foto', 'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guruMapels()
    {
        return $this->hasMany(GuruMapel::class);
    }

    public function jadwals()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function jurnals()
    {
        return $this->hasMany(JurnalGuru::class);
    }

    public function mataPelajarans()
    {
        return $this->belongsToMany(MataPelajaran::class, 'guru_mapels');
    }
}