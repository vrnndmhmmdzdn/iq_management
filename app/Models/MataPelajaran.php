<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'kode', 'tingkat'];

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }
    public function guruMapel()
    {
        return $this->hasOne(GuruMapel::class);
    }
}