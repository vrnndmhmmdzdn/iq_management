<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalLampiran extends Model
{
    protected $fillable = [
        'jurnal_guru_id', 'nama_file', 'path', 'tipe',
    ];

    public function jurnal()
    {
        return $this->belongsTo(JurnalGuru::class, 'jurnal_guru_id');
    }
}