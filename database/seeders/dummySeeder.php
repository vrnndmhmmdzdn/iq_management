<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\OrangTua;
use App\Models\TahunAjaran;
use App\Models\MataPelajaran;
use App\Models\GuruMapel;
use App\Models\JadwalPelajaran;
use App\Models\Absensi;
use App\Models\Nilai;
use App\Models\Tugas;
use App\Models\PembayaranSpp;
use App\Models\JurnalGuru;
use App\Models\Ekskul;
use Spatie\Permission\Models\Role;

class DummySeeder extends Seeder
{
    public function run(): void
    {
        // ── 0. ROLES ──────────────────────────────────────────────────────
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'guru']);
        Role::firstOrCreate(['name' => 'ortu']);

        // ── 1. TAHUN AJARAN ───────────────────────────────────────────────
        $tahunAjaran = TahunAjaran::firstOrCreate(
            ['nama' => '2024/2025'],
            [
                'tanggal_mulai'   => '2024-07-15',
                'tanggal_selesai' => '2025-06-30',
                'is_aktif'        => true,
            ]
        );

        // ── 2. USERS & GURU ───────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@iqmanagement.sch.id'],
            [
                'name'               => 'Administrator',
                'passwor    d'           => Hash::make('password'),
                'email_verified_at'  => now(),
            ]
        );
        $admin->assignRole('admin');

        // Data guru lengkap
        $guruData = [
            ['name' => 'Ustadz Zaidan',  'email' => 'zaidan@iqmanagement.sch.id',  'nip' => '198501012010011001', 'jk' => 'L'],
            ['name' => 'Ustadzah Nur',   'email' => 'nur@iqmanagement.sch.id',     'nip' => '198703022012012002', 'jk' => 'P'],
            ['name' => 'Ust Ismail',     'email' => 'ismail@iqmanagement.sch.id',  'nip' => '199001032013011003', 'jk' => 'L'],
            ['name' => 'UStadz Thufail', 'email' => 'thufail@iqmanagement.sch.id', 'nip' => '199205042014011004', 'jk' => 'L'],
            ['name' => 'Ustadzah Khansa','email' => 'khansa@iqmanagement.sch.id',  'nip' => '199407052015012005', 'jk' => 'P'],
            ['name' => 'Ust Muhammad',   'email' => 'muhammad@iqmanagement.sch.id','nip' => '199609062016011006', 'jk' => 'L'],
        ];

        $guruUsers = [];
        $guruModels = [];

        foreach ($guruData as $i => $g) {
            $user = User::firstOrCreate(
                ['email' => $g['email']],
                [
                    'name'     => $g['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('guru');
            $guruUsers[] = $user;

            $guru = Guru::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nip'           => $g['nip'],
                    'nama_lengkap'  => $g['name'],
                    'jenis_kelamin' => $g['jk'],
                    'tempat_lahir'  => 'Surakarta',
                    'tanggal_lahir' => '199' . $i . '-0' . ($i + 1) . '-15',
                    'alamat'        => 'Jl. Guru No. ' . ($i + 1) . ', Surakarta',
                    'no_hp'         => '0812345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'status'        => 'aktif',
                ]
            );
            $guruModels[] = $guru;
        }

        // User ortu demo
        $ortuUser = User::firstOrCreate(
            ['email' => 'ortu@iqmanagement.sch.id'],
            [
                'name'     => 'Orang Tua Demo',
                'password' => Hash::make('password'),
            ]
        );
        $ortuUser->assignRole('ortu');

        // ── 3. MATA PELAJARAN ─────────────────────────────────────────────
        $mapelData = [
            ['nama' => 'Matematika',       'kode' => 'MTK', 'tingkat' => 0],
            ['nama' => 'Bahasa Indonesia', 'kode' => 'BIN', 'tingkat' => 0],
            ['nama' => 'Bahasa Arab',      'kode' => 'BAR', 'tingkat' => 0],
            ['nama' => 'Al-Quran Hadits',  'kode' => 'AQH', 'tingkat' => 0],
            ['nama' => 'Fiqih',            'kode' => 'FQH', 'tingkat' => 0],
            ['nama' => 'Aqidah Akhlak',    'kode' => 'AAK', 'tingkat' => 0],
            ['nama' => 'IPA',              'kode' => 'IPA', 'tingkat' => 0],
            ['nama' => 'IPS',              'kode' => 'IPS', 'tingkat' => 0],
            ['nama' => 'PJOK',             'kode' => 'PJK', 'tingkat' => 0],
            ['nama' => 'SBdP',             'kode' => 'SBD', 'tingkat' => 0],
            ['nama' => 'Bahasa Inggris',   'kode' => 'BIG', 'tingkat' => 0],
            ['nama' => 'PKn',              'kode' => 'PKN', 'tingkat' => 0],
        ];

        $mapels = [];
        foreach ($mapelData as $m) {
            $mapels[] = MataPelajaran::firstOrCreate(['kode' => $m['kode']], $m);
        }

        // ── 4. GURU MAPEL (tiap guru pegang 2 mapel) ─────────────────────
        $guruMapelAssign = [
            0 => [0, 1],  // Zaidan: MTK, BIN
            1 => [2, 3],  // Nur: BAR, AQH
            2 => [4, 5],  // Ismail: FQH, AAK
            3 => [6, 7],  // Thufail: IPA, IPS
            4 => [8, 9],  // Khansa: PJOK, SBdP
            5 => [10, 11],// Muhammad: BIG, PKn
        ];

        foreach ($guruMapelAssign as $guruIdx => $mapelIdxs) {
            foreach ($mapelIdxs as $mapelIdx) {
                GuruMapel::firstOrCreate([
                    'guru_id'          => $guruModels[$guruIdx]->id,
                    'mata_pelajaran_id'=> $mapels[$mapelIdx]->id,
                ]);
            }
        }

        // ── 5. KELAS ─────────────────────────────────────────────────────
        $kelasData = [
            ['nama_kelas' => 'Kelas 1A', 'tingkat' => 1, 'wali' => 0],
            ['nama_kelas' => 'Kelas 1B', 'tingkat' => 1, 'wali' => 1],
            ['nama_kelas' => 'Kelas 2A', 'tingkat' => 2, 'wali' => 2],
            ['nama_kelas' => 'Kelas 2B', 'tingkat' => 2, 'wali' => 3],
            ['nama_kelas' => 'Kelas 3A', 'tingkat' => 3, 'wali' => 4],
            ['nama_kelas' => 'Kelas 3B', 'tingkat' => 3, 'wali' => 5],
            ['nama_kelas' => 'Kelas 4A', 'tingkat' => 4, 'wali' => 0],
            ['nama_kelas' => 'Kelas 5A', 'tingkat' => 5, 'wali' => 1],
            ['nama_kelas' => 'Kelas 6A', 'tingkat' => 6, 'wali' => 2],
        ];

        $kelasList = [];
        foreach ($kelasData as $k) {
            $kelasList[] = Kelas::firstOrCreate(
                ['nama_kelas' => $k['nama_kelas'], 'tahun_ajaran_id' => $tahunAjaran->id],
                [
                    'tingkat'         => $k['tingkat'],
                    'wali_kelas_id'   => $guruUsers[$k['wali']]->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                ]
            );
        }

        // ── 6. SISWA ─────────────────────────────────────────────────────
        $namaSiswaL = [
            'Ahmad Fauzi', 'Muhammad Rizki', 'Abdullah Hakim', 'Bilal Ramadhan',
            'Umar Farouq', 'Hasan Basri', 'Yusuf Ali', 'Ibrahim Malik',
            'Khalid Anwar', 'Hamzah Putra', 'Salman Faris', 'Zaid Mubarak',
            'Anas Wahyu', 'Rafi Hidayat', 'Faiz Akbar', 'Daffa Pratama',
            'Raihan Putra', 'Gibran Naufal', 'Alif Ramadhan', 'Bagas Wicaksono',
        ];
        $namaSiswaP = [
            'Siti Aisyah', 'Fatimah Zahra', 'Khadijah Nur', 'Zainab Salma',
            'Maryam Fitri', 'Ruqayyah Dewi', 'Asma Wahyu', 'Sumayyah Putri',
            'Hafsa Rahmah', 'Aisyah Putri', 'Syifa Aulia', 'Naila Rahma',
            'Azizah Salma', 'Nabila Husna', 'Zahra Aulia', 'Nadia Putri',
            'Salma Azizah', 'Hana Maharani', 'Rania Putri', 'Delia Safira',
        ];

        $nisCounter = 1;
        $lIdx = 0;
        $pIdx = 0;
        $siswas = [];
        $siswaDemoOrtu = null;

        foreach ($kelasList as $ki => $kelas) {
            $jumlah = ($ki < 2) ? 8 : 5; // kelas 1A & 1B lebih banyak

            for ($i = 0; $i < $jumlah; $i++) {
                $jk = ($i % 2 === 0) ? 'L' : 'P';
                $nama = $jk === 'L'
                    ? ($namaSiswaL[$lIdx++ % count($namaSiswaL)] . ($lIdx > count($namaSiswaL) ? ' ' . $lIdx : ''))
                    : ($namaSiswaP[$pIdx++ % count($namaSiswaP)] . ($pIdx > count($namaSiswaP) ? ' ' . $pIdx : ''));

                $siswa = Siswa::firstOrCreate(
                    ['nis' => '2024' . str_pad($nisCounter, 3, '0', STR_PAD_LEFT)],
                    [
                        'nisn'            => '123456' . str_pad($nisCounter, 4, '0', STR_PAD_LEFT),
                        'nama_lengkap'    => $nama,
                        'jenis_kelamin'   => $jk,
                        'tempat_lahir'    => 'Surakarta',
                        'tanggal_lahir'   => '201' . rand(5, 8) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                        'alamat'          => 'Jl. Mawar No. ' . $nisCounter . ', Surakarta',
                        'status'          => 'aktif',
                        'nominal_spp'     => 350000,
                        'kelas_id'        => $kelas->id,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                    ]
                );

                // Siswa pertama kelas 1A jadi demo ortu
                if ($ki === 0 && $i === 0) {
                    $siswaDemoOrtu = $siswa;
                }

                $siswas[] = $siswa;
                $nisCounter++;
            }
        }

        // ── 7. ORANG TUA ─────────────────────────────────────────────────
        OrangTua::firstOrCreate(
            ['user_id' => $ortuUser->id, 'siswa_id' => $siswaDemoOrtu->id],
            [
                'nama'      => 'Bapak Demo',
                'hubungan'  => 'ayah',
                'no_hp'     => '08123456789',
                'pekerjaan' => 'Wiraswasta',
                'alamat'    => 'Jl. Mawar No. 1, Surakarta',
            ]
        );

        // Ortu tambahan (tanpa akun)
        foreach (array_slice($siswas, 1, 10) as $s) {
            OrangTua::firstOrCreate(
                ['siswa_id' => $s->id, 'hubungan' => 'ayah'],
                [
                    'user_id'   => null,
                    'nama'      => 'Ortu ' . $s->nama_lengkap,
                    'hubungan'  => 'ayah',
                    'no_hp'     => '0813' . rand(10000000, 99999999),
                    'pekerjaan' => ['PNS', 'Wiraswasta', 'Guru', 'Dokter', 'TNI'][rand(0, 4)],
                    'alamat'    => 'Jl. Demo No. ' . rand(1, 99) . ', Surakarta',
                ]
            );
        }

        // ── 8. JADWAL PELAJARAN ───────────────────────────────────────────
        $haris = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
        $jamSlot = [
            ['07:00', '08:30'],
            ['08:30', '10:00'],
            ['10:15', '11:45'],
            ['13:00', '14:30'],
        ];

        foreach ($guruMapelAssign as $guruIdx => $mapelIdxs) {
            $guru = $guruModels[$guruIdx];

            foreach ($haris as $hi => $hari) {
                $kelasTarget = $kelasList[$hi % count($kelasList)];
                $jamIdx      = $guruIdx % count($jamSlot);
                $mapelIdx    = $mapelIdxs[$hi % count($mapelIdxs)];

                JadwalPelajaran::firstOrCreate(
                    [
                        'guru_id'          => $guru->id,
                        'hari'             => $hari,
                        'jam_mulai'        => $jamSlot[$jamIdx][0],
                        'tahun_ajaran_id'  => $tahunAjaran->id,
                    ],
                    [
                        'kelas_id'         => $kelasTarget->id,
                        'mata_pelajaran_id'=> $mapels[$mapelIdx]->id,
                        'jam_selesai'      => $jamSlot[$jamIdx][1],
                    ]
                );
            }
        }

        // ── 9. ABSENSI (30 hari terakhir, skip weekend) ───────────────────
        $statusPool = ['hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alfa'];
        $siswaKelas1A = Siswa::where('kelas_id', $kelasList[0]->id)->get();

        for ($hari = 29; $hari >= 0; $hari--) {
            $tanggal   = now()->subDays($hari);
            $dayOfWeek = $tanggal->dayOfWeek;
            if ($dayOfWeek === 0 || $dayOfWeek === 6) continue;

            foreach ($siswaKelas1A as $siswa) {
                $status = ($siswa->id === $siswaDemoOrtu->id)
                    ? 'hadir'
                    : $statusPool[array_rand($statusPool)];

                Absensi::firstOrCreate(
                    ['siswa_id' => $siswa->id, 'tanggal' => $tanggal->format('Y-m-d')],
                    [
                        'kelas_id'        => $kelasList[0]->id,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'status'          => $status,
                        'keterangan'      => $status !== 'hadir' ? 'Keterangan ' . $status : null,
                        'dicatat_oleh'    => $guruUsers[0]->id,
                    ]
                );
            }
        }

        // ── 10. NILAI ─────────────────────────────────────────────────────
        $jenisNilai  = ['harian', 'tugas', 'uts', 'uas'];
        $siswaKelas1 = Siswa::where('kelas_id', $kelasList[0]->id)->get();

        foreach ($mapels as $mi => $mapel) {
            if ($mi >= 4) break; // limit 4 mapel

            foreach ($jenisNilai as $jenis) {
                $tanggalUjian = now()->subDays(rand(5, 60))->format('Y-m-d');

                foreach ($siswaKelas1 as $siswa) {
                    Nilai::firstOrCreate(
                        [
                            'siswa_id'         => $siswa->id,
                            'mata_pelajaran_id'=> $mapel->id,
                            'jenis'            => $jenis,
                            'tanggal_ujian'    => $tanggalUjian,
                            'tahun_ajaran_id'  => $tahunAjaran->id,
                        ],
                        [
                            'kelas_id'    => $kelasList[0]->id,
                            'nilai'       => rand(60, 100),
                            'keterangan'  => null,
                            'dicatat_oleh'=> $guruUsers[0]->id,
                        ]
                    );
                }
            }
        }

        // ── 11. TUGAS ─────────────────────────────────────────────────────
        $judulTugas = [
            'Latihan Soal Pecahan',
            'Membuat Puisi Bebas',
            'Hafalan Surat Al-Mulk',
            'Rangkuman Bab 3',
            'Praktik Wudhu',
            'Gambar Poster Lingkungan',
            'Soal Pilihan Ganda IPA',
            'Diskusi Tokoh Sejarah',
            'Mading Kelas',
            'Laporan Pengamatan',
        ];

        foreach ($judulTugas as $i => $judul) {
            $tanggal = now()->subDays($i * 2);
            Tugas::firstOrCreate(
                ['judul' => $judul, 'kelas_id' => $kelasList[$i % count($kelasList)]->id],
                [
                    'deskripsi'        => 'Kerjakan tugas berikut dengan baik dan teliti. ' . $judul,
                    'mata_pelajaran_id'=> $mapels[$i % count($mapels)]->id,
                    'created_by'       => $guruUsers[$i % count($guruUsers)]->id,
                    'tanggal'          => $tanggal->format('Y-m-d'),
                    'batas_waktu'      => '14:00:00',
                    'is_aktif'         => true,
                ]
            );
        }

        // ── 12. PEMBAYARAN SPP ────────────────────────────────────────────
        // Demo ortu — 3 bulan terakhir dikonfirmasi
        for ($bulan = 3; $bulan >= 1; $bulan--) {
            $periode = now()->subMonths($bulan)->format('Y-m');
            PembayaranSpp::firstOrCreate(
                ['siswa_id' => $siswaDemoOrtu->id, 'periode' => $periode],
                [
                    'user_id'           => $ortuUser->id,
                    'nominal'           => 350000,
                    'bukti_pembayaran'  => 'dummy/bukti_dummy.jpg',
                    'status'            => 'dikonfirmasi',
                    'catatan_ortu'      => 'Pembayaran SPP ' . $periode,
                    'dikonfirmasi_oleh' => $admin->id,
                    'dikonfirmasi_at'   => now()->subMonths($bulan),
                ]
            );
        }

        // Bulan ini - menunggu
        PembayaranSpp::firstOrCreate(
            ['siswa_id' => $siswaDemoOrtu->id, 'periode' => now()->format('Y-m')],
            [
                'user_id'          => $ortuUser->id,
                'nominal'          => 350000,
                'bukti_pembayaran' => 'dummy/bukti_dummy.jpg',
                'status'           => 'menunggu',
                'catatan_ortu'     => 'Mohon segera dikonfirmasi',
            ]
        );

        // SPP siswa lain - variasi status
        $statusSpp = ['dikonfirmasi', 'menunggu', 'ditolak', 'dikonfirmasi'];
        foreach (array_slice($siswas, 1, 8) as $i => $siswa) {
            $periode = now()->subMonths($i % 2)->format('Y-m');
            PembayaranSpp::firstOrCreate(
                ['siswa_id' => $siswa->id, 'periode' => $periode],
                [
                    'user_id'           => $ortuUser->id,
                    'nominal'           => 350000,
                    'bukti_pembayaran'  => 'dummy/bukti_dummy.jpg',
                    'status'            => $statusSpp[$i % count($statusSpp)],
                    'dikonfirmasi_oleh' => $admin->id,
                    'dikonfirmasi_at'   => now(),
                ]
            );
        }

        // ── 13. JURNAL GURU ───────────────────────────────────────────────
        $materiList = [
            'Pengenalan Bilangan Cacah',
            'Penjumlahan dan Pengurangan',
            'Pengenalan Huruf Hijaiyah',
            'Tajwid Nun Sukun',
            'Hukum Bacaan Mad',
            'Sholat Berjamaah',
            'Tata Cara Wudhu',
            'Makna Surat Al-Fatihah',
            'Kisah Nabi Ibrahim',
            'Akhlak Terpuji',
        ];

        $metodeList = ['ceramah', 'diskusi', 'praktik', 'tanya_jawab', 'demonstrasi'];

        // Jurnal 10 hari terakhir per guru
        foreach ($guruModels as $gi => $guru) {
            $jadwals = JadwalPelajaran::where('guru_id', $guru->id)->get();
            if ($jadwals->isEmpty()) continue;

            for ($hari = 9; $hari >= 0; $hari--) {
                $tanggal   = now()->subDays($hari);
                if ($tanggal->dayOfWeek === 0 || $tanggal->dayOfWeek === 6) continue;

                $jadwalHariIni = $jadwals->where('hari', strtolower(
                    $tanggal->locale('id')->translatedFormat('l')
                ))->first();

                if (! $jadwalHariIni) continue;

                // Jurnal guru_id masih referensi ke users (sesuai migration)
                JurnalGuru::firstOrCreate(
                    [
                        'guru_id'          => $guruUsers[$gi]->id,
                        'kelas_id'         => $jadwalHariIni->kelas_id,
                        'mata_pelajaran_id'=> $jadwalHariIni->mata_pelajaran_id,
                        'tanggal'          => $tanggal->format('Y-m-d'),
                    ],
                    [
                        'tahun_ajaran_id'    => $tahunAjaran->id,
                        'pertemuan_ke'       => (10 - $hari),
                        'materi'             => $materiList[($gi + $hari) % count($materiList)],
                        'kompetensi_dasar'   => '3.' . (($gi + $hari) % 5 + 1) . ' Memahami konsep dasar materi pembelajaran',
                        'indikator_pencapaian'=> 'Siswa mampu menjelaskan dan mempraktikkan materi yang diajarkan',
                        'deskripsi_kegiatan' => 'Kegiatan pembukaan: doa dan absensi (10 menit). Kegiatan inti: penjelasan materi dan latihan soal (60 menit). Kegiatan penutup: evaluasi dan kesimpulan (20 menit).',
                        'metode_pembelajaran'=> $metodeList[($gi + $hari) % count($metodeList)],
                        'media_pembelajaran' => ['Proyektor, Papan Tulis', 'Modul, LKS', 'Video Pembelajaran', 'Alat Peraga'][rand(0, 3)],
                        'jam_mulai'          => $jadwalHariIni->jam_mulai,
                        'jam_selesai'        => $jadwalHariIni->jam_selesai,
                        'jumlah_hadir'       => rand(20, 28),
                        'jumlah_tidak_hadir' => rand(0, 5),
                        'capaian'            => ['tercapai', 'tercapai', 'tercapai', 'sebagian', 'belum'][rand(0, 4)],
                        'penilaian_dilakukan'=> rand(0, 1),
                        'jenis_penilaian'    => ['tertulis', 'lisan', 'observasi', null][rand(0, 3)],
                        'tindak_lanjut'      => 'Pertemuan berikutnya akan membahas lanjutan materi dan mengadakan kuis singkat.',
                        'catatan'            => rand(0, 1) ? 'Beberapa siswa masih perlu bimbingan tambahan.' : null,
                        'status'             => $hari > 2 ? 'submitted' : 'draft',
                        'submitted_at'       => $hari > 2 ? $tanggal->addHours(2) : null,
                    ]
                );
            }
        }

        // ── 14. EKSKUL ───────────────────────────────────────────────────
        $ekskulData = [
            ['nama_ekskul' => 'Pramuka',          'deskripsi' => 'Kegiatan kepramukaan untuk membentuk karakter siswa'],
            ['nama_ekskul' => 'Tahfidz',           'deskripsi' => 'Program hafalan Al-Quran untuk siswa berprestasi'],
            ['nama_ekskul' => 'Kaligrafi',         'deskripsi' => 'Seni menulis indah huruf Arab dan latin'],
            ['nama_ekskul' => 'Futsal',            'deskripsi' => 'Olahraga futsal untuk siswa laki-laki'],
            ['nama_ekskul' => 'Badminton',         'deskripsi' => 'Olahraga bulutangkis untuk semua siswa'],
            ['nama_ekskul' => 'Seni Lukis',        'deskripsi' => 'Mengembangkan bakat seni lukis siswa'],
        ];

        foreach ($ekskulData as $i => $e) {
            Ekskul::firstOrCreate(
                ['nama_ekskul' => $e['nama_ekskul']],
                [
                    'deskripsi'  => $e['deskripsi'],
                    'pembina_id' => $guruModels[$i % count($guruModels)]->id,
                    'is_aktif'   => true,
                ]
            );
        }

        $this->command->info('✅ DummySeeder selesai!');
        $this->command->info('   Users    : ' . User::count());
        $this->command->info('   Guru     : ' . Guru::count());
        $this->command->info('   Kelas    : ' . Kelas::count());
        $this->command->info('   Siswa    : ' . Siswa::count());
        $this->command->info('   Jadwal   : ' . JadwalPelajaran::count());
        $this->command->info('   Absensi  : ' . Absensi::count());
        $this->command->info('   Nilai    : ' . Nilai::count());
        $this->command->info('   Tugas    : ' . Tugas::count());
        $this->command->info('   SPP      : ' . PembayaranSpp::count());
        $this->command->info('   Jurnal   : ' . JurnalGuru::count());
        $this->command->info('   Ekskul   : ' . Ekskul::count());
    }
}