<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'tanggal_mulai', 'tanggal_selesai', 'is_aktif'];

    protected $casts = ['is_aktif' => 'boolean'];

    public static function aktif()
    {
        return static::where('is_aktif', true)->first();
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}
