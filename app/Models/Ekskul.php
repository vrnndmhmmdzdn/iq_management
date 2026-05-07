<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ekskul extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_ekskul',
        'deskripsi',
        'pembina_id',
        'is_aktif',
    ];
    protected $casts = ['is_aktif' => 'boolean'];

    public function pembina()
    {
        return $this->belongsTo(Guru::class );
    }
}
