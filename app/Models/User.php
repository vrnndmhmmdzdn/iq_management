<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Hanya admin yang bisa akses Filament panel
    public function canAccessPanel(Panel $panel): bool
    {
        // return $this->hasAnyRole('admin', 'guru');
        // if ($panel->getId() === 'admin') {
        //     return $this->role === 'admin'; // Sesuaikan dengan kolom role Anda
        // }
        return $this->hasAnyRole(['admin', 'guru']);
        // return true;
    }

    public function orangTua()
    {
        return $this->hasOne(OrangTua::class);
    }
    // public function guruMapels()
    // {
    //     return $this->hasMany(GuruMapel::class, 'guru_id');
    // }
    // app/Models/User.php
    public function guru()
    {
        return $this->hasOne(Guru::class);
    }
}