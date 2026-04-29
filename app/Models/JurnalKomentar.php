<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalKomentar extends Model
{
    protected $fillable = [
        'jurnal_guru_id', 'user_id', 'komentar',
    ];

    public function jurnal()
    {
        return $this->belongsTo(JurnalGuru::class, 'jurnal_guru_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}