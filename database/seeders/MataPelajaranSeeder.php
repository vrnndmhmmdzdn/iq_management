<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $mapel = [
            ['nama' => 'Matematika',           'kode' => 'MTK', 'tingkat' => 0],
            ['nama' => 'Bahasa Indonesia',     'kode' => 'BIN', 'tingkat' => 0],
            ['nama' => 'Bahasa Arab',          'kode' => 'BAR', 'tingkat' => 0],
            ['nama' => 'Al-Quran Hadits',      'kode' => 'AQH', 'tingkat' => 0],
            ['nama' => 'Fiqih',                'kode' => 'FQH', 'tingkat' => 0],
            ['nama' => 'Aqidah Akhlak',        'kode' => 'AAK', 'tingkat' => 0],
            ['nama' => 'IPA',                  'kode' => 'IPA', 'tingkat' => 0],
            ['nama' => 'IPS',                  'kode' => 'IPS', 'tingkat' => 0],
            ['nama' => 'PJOK',                 'kode' => 'PJK', 'tingkat' => 0],
            ['nama' => 'SBdP',                 'kode' => 'SBD', 'tingkat' => 0],
            ['nama' => 'Bahasa Inggris',       'kode' => 'BIG', 'tingkat' => 0],
            ['nama' => 'PKn',                  'kode' => 'PKN', 'tingkat' => 0],
        ];

        foreach ($mapel as $m) {
            MataPelajaran::create($m);
        }
    }
}