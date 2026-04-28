<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\GuruMapel;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class NilaiOrtuController extends Controller
{
    public function index(Request $request)
    {
        $user  = auth()->user();
        $ortu  = $user->orangTua;
        $siswa = $ortu?->siswa;

        if (! $siswa) abort(403, 'Data siswa tidak ditemukan.');

        $tahunAjaranId = $request->get('tahun_ajaran_id',
            TahunAjaran::where('is_aktif', true)->first()?->id
        );

        $activeTab   = $request->get('tab', 'harian');
        $mapelFilter = $request->get('mapel_filter');

        // Semua mapel untuk dropdown
        $mapelList = MataPelajaran::orderBy('nama')->get();

        // Nilai siswa ini sesuai tab & filter
        $nilaiSiswa = Nilai::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('jenis', $activeTab)
            ->when($mapelFilter, fn($q) => $q->where('mata_pelajaran_id', $mapelFilter))
            ->get()
            ->keyBy(fn($n) => $n->mata_pelajaran_id . '_' . $n->tanggal_ujian);


        // Rata-rata kelas per mapel untuk jenis ini
        $rataRataKelas = Nilai::where('kelas_id', $siswa->kelas_id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('jenis', $activeTab)
            ->when($mapelFilter, fn($q) => $q->where('mata_pelajaran_id', $mapelFilter))
            ->selectRaw('mata_pelajaran_id, tanggal_ujian, ROUND(AVG(nilai), 1) as rata_rata')
            ->groupBy('mata_pelajaran_id', 'tanggal_ujian')
            ->get()
            ->keyBy(fn($n) => $n->mata_pelajaran_id . '_' . $n->tanggal_ujian);

        // Guru mapel
        $guruMapels = GuruMapel::with('guru')
        ->get()
        ->keyBy('mata_pelajaran_id');

         // Ambil semua kombinasi (mapel, tanggal) yang ada di kelas ini
        $kombinasi = Nilai::where('kelas_id', $siswa->kelas_id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('jenis', $activeTab)
            ->when($mapelFilter, fn($q) => $q->where('mata_pelajaran_id', $mapelFilter))
            ->selectRaw('DISTINCT mata_pelajaran_id, tanggal_ujian')
            ->orderBy('tanggal_ujian')
            ->get();

        if ($kombinasi->isEmpty()) {
            $nilaiTabed = collect();
        } else {
            $mapelIds = $kombinasi->pluck('mata_pelajaran_id')->unique();
            $mapels   = MataPelajaran::whereIn('id', $mapelIds)->get()->keyBy('id');

            $nilaiTabed = $kombinasi->map(function ($item) use ($nilaiSiswa, $rataRataKelas, $guruMapels, $mapels) {
                $key   = $item->mata_pelajaran_id . '_' . $item->tanggal_ujian;
                $mapel = $mapels->get($item->mata_pelajaran_id);

                return (object)[
                    'mapel_id'        => $item->mata_pelajaran_id,
                    'mapel_nama'      => $mapel?->nama ?? '-',
                    'guru_nama'       => $guruMapels->get($item->mata_pelajaran_id)?->guru?->name ?? '-',
                    'nilai'           => $nilaiSiswa->get($key)?->nilai,
                    'keterangan'      => $nilaiSiswa->get($key)?->keterangan,
                    'rata_rata_kelas' => $rataRataKelas->get($key)?->rata_rata ?? '-',
                    'tanggal_ujian'   => \Carbon\Carbon::parse($item->tanggal_ujian)->translatedFormat('d M Y'),
                ];
            });
        }

        $tahunAjarans = TahunAjaran::orderByDesc('is_aktif')->get();

        return view('ortu.nilai.index', compact(
            'siswa', 'nilaiTabed', 'tahunAjarans', 'tahunAjaranId',
            'activeTab', 'mapelFilter', 'mapelList'
        ));
    }
}