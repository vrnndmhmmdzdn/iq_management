<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\OrangTua;
use App\Models\TahunAjaran;
use App\Models\PembayaranSpp;
use App\Models\Absensi;

class DummySeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        $guru        = User::where('email', 'guru@iqmanagement.sch.id')->first();
        $ortu        = User::where('email', 'ortu@iqmanagement.sch.id')->first();
        $admin       = User::where('email', 'admin@iqmanagement.sch.id')->first();

        // ── 1. KELAS ─────────────────────────────────────────────────────
        $kelasData = [
            ['nama_kelas' => 'Kelas 1A', 'tingkat' => 1],
            ['nama_kelas' => 'Kelas 2A', 'tingkat' => 2],
            ['nama_kelas' => 'Kelas 3A', 'tingkat' => 3],
            ['nama_kelas' => 'Kelas 4A', 'tingkat' => 4],
            ['nama_kelas' => 'Kelas 5A', 'tingkat' => 5],
            ['nama_kelas' => 'Kelas 6A', 'tingkat' => 6],
        ];

        $kelasList = [];
        foreach ($kelasData as $k) {
            $kelasList[] = Kelas::create([
                'nama_kelas'      => $k['nama_kelas'],
                'tingkat'         => $k['tingkat'],
                'wali_kelas_id'   => $guru->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]);
        }

        // Kelas 1A untuk demo ortu
        $kelasDemoOrtu = $kelasList[0];

        // ── 2. SISWA ─────────────────────────────────────────────────────
        $siswaDemoData = [
            // Siswa yang terhubung ke akun ortu demo
            [
                'nis'           => '2024001',
                'nisn'          => '1234567890',
                'nama_lengkap'  => 'Ahmad Fauzi',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Surakarta',
                'tanggal_lahir' => '2017-05-10',
                'alamat'        => 'Jl. Mawar No. 1, Surakarta',
                'status'        => 'aktif',
                'kelas_id'      => $kelasDemoOrtu->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ],
        ];

        // Siswa tambahan di kelas 1A
        $siswaLainData = [
            ['nis' => '2024002', 'nisn' => '1234567891', 'nama_lengkap' => 'Siti Aisyah',     'jenis_kelamin' => 'P'],
            ['nis' => '2024003', 'nisn' => '1234567892', 'nama_lengkap' => 'Muhammad Rizki',  'jenis_kelamin' => 'L'],
            ['nis' => '2024004', 'nisn' => '1234567893', 'nama_lengkap' => 'Fatimah Zahra',   'jenis_kelamin' => 'P'],
            ['nis' => '2024005', 'nisn' => '1234567894', 'nama_lengkap' => 'Abdullah Hakim',  'jenis_kelamin' => 'L'],
        ];

        // Buat siswa demo (terhubung ke ortu)
        $siswaDemoOrtu = Siswa::create($siswaDemoData[0]);

        // Buat siswa lainnya di kelas 1A
        foreach ($siswaLainData as $s) {
            Siswa::create([
                'nis'             => $s['nis'],
                'nisn'            => $s['nisn'],
                'nama_lengkap'    => $s['nama_lengkap'],
                'jenis_kelamin'   => $s['jenis_kelamin'],
                'tempat_lahir'    => 'Surakarta',
                'tanggal_lahir'   => '2017-06-15',
                'alamat'          => 'Jl. Melati No. 2, Surakarta',
                'status'          => 'aktif',
                'kelas_id'        => $kelasDemoOrtu->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]);
        }

        // Siswa di kelas lain (masing-masing 3 siswa)
        $namaLain = [
            'Bilal Ramadhan', 'Khadijah Nur', 'Umar Farouq',
            'Zainab Salma', 'Hasan Basri', 'Maryam Fitri',
            'Yusuf Ali', 'Ruqayyah Dewi', 'Ibrahim Malik',
            'Asma Wahyu', 'Khalid Anwar', 'Sumayyah Putri',
            'Hamzah Putra', 'Hafsa Rahmah', 'Salman Faris',
        ];

        $nisCounter = 6;
        $namaIndex = 0;
        foreach (array_slice($kelasList, 1) as $kelas) {
            for ($i = 0; $i < 3; $i++) {
                $namaVal = $namaLain[$namaIndex] ?? 'Siswa Demo';
                $namaIndex++;
                Siswa::create([
                    'nis'             => '2024' . str_pad($nisCounter, 3, '0', STR_PAD_LEFT),
                    'nisn'            => '123456' . str_pad($nisCounter, 4, '0', STR_PAD_LEFT),
                    'nama_lengkap'    => $namaVal,
                    'jenis_kelamin'   => $nisCounter % 2 === 0 ? 'L' : 'P',
                    'tempat_lahir'    => 'Surakarta',
                    'tanggal_lahir'   => '2016-03-20',
                    'alamat'          => 'Jl. Demo No. ' . $nisCounter . ', Surakarta',
                    'status'          => 'aktif',
                    'kelas_id'        => $kelas->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                ]);
                $nisCounter++;
            }
        }

        // ── 3. ORANG TUA (link ke akun ortu demo) ────────────────────────
        OrangTua::create([
            'user_id'   => $ortu->id,
            'siswa_id'  => $siswaDemoOrtu->id,
            'nama'      => 'Orang Tua Demo',
            'hubungan'  => 'ayah',
            'no_hp'     => '08123456789',
            'pekerjaan' => 'Wiraswasta',
            'alamat'    => 'Jl. Mawar No. 1, Surakarta',
        ]);

        // ── 4. PEMBAYARAN SPP ─────────────────────────────────────────────
        // Bulan lalu - sudah dikonfirmasi
        PembayaranSpp::create([
            'siswa_id'          => $siswaDemoOrtu->id,
            'user_id'           => $ortu->id,
            'periode'           => now()->subMonth()->format('Y-m'),
            'nominal'           => 150000,
            'bukti_pembayaran'  => 'dummy/bukti_dummy.jpg', // ← tambah ini
            'status'            => 'dikonfirmasi',
            'catatan_ortu'      => 'Pembayaran bulan lalu',
            'dikonfirmasi_oleh' => $admin->id,
            'dikonfirmasi_at'   => now()->subMonth(),
        ]);

        // Bulan ini - sudah dikonfirmasi
        PembayaranSpp::create([
            'siswa_id'          => $siswaDemoOrtu->id,
            'user_id'           => $ortu->id,
            'periode'           => now()->format('Y-m'),
            'nominal'           => 150000,
            'bukti_pembayaran'  => 'dummy/bukti_dummy.jpg', // ← tambah ini
            'status'            => 'dikonfirmasi',
            'catatan_ortu'      => 'Pembayaran bulan ini',
            'dikonfirmasi_oleh' => $admin->id,
            'dikonfirmasi_at'   => now(),
        ]);

        // ── 5. ABSENSI (7 hari terakhir) ─────────────────────────────────
        $semuaSiswaKelas1 = Siswa::where('kelas_id', $kelasDemoOrtu->id)->get();
        $statusPool = ['hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alfa'];

        for ($hari = 6; $hari >= 0; $hari--) {
            $tanggal = now()->subDays($hari)->format('Y-m-d');

            // Skip sabtu minggu
            $dayOfWeek = now()->subDays($hari)->dayOfWeek;
            if ($dayOfWeek === 0 || $dayOfWeek === 6) continue;

            foreach ($semuaSiswaKelas1 as $siswa) {
                // Siswa demo selalu hadir
                $status = $siswa->id === $siswaDemoOrtu->id
                    ? 'hadir'
                    : $statusPool[array_rand($statusPool)];

                Absensi::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'tanggal' => $tanggal],
                    [
                        'kelas_id'        => $kelasDemoOrtu->id,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'status'          => $status,
                        'dicatat_oleh'    => $guru->id,
                    ]
                );
            }
        }
    }
}