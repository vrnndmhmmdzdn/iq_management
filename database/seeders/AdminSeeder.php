<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TahunAjaran;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@iqmanagement.sch.id',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $guru = User::create([
            'name'     => 'Guru Demo',
            'email'    => 'guru@iqmanagement.sch.id',
            'password' => bcrypt('password'),
        ]);
        $guru->assignRole('guru');

        $ortu = User::create([
            'name'     => 'Orang Tua Demo',
            'email'    => 'ortu@iqmanagement.sch.id',
            'password' => bcrypt('password'),
        ]);
        $ortu->assignRole('ortu');

        TahunAjaran::create([
            'nama'            => '2024/2025',
            'tanggal_mulai'   => '2024-07-15',
            'tanggal_selesai' => '2025-06-30',
            'is_aktif'        => true,
        ]);
    }
}
