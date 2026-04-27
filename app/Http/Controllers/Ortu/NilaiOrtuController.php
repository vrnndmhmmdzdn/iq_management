<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
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

        if (! $siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        $tahunAjaranId = $request->get('tahun_ajaran_id',
            TahunAjaran::where('is_aktif', true)->first()?->id
        );

        $nilais = Nilai::with('mataPelajaran')
            ->where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->orderBy('mata_pelajaran_id')
            ->orderBy('jenis')
            ->get();

        $nilaiGrouped = $nilais->groupBy('mata_pelajaran_id')->map(function ($rows) {
            $mapel = $rows->first()->mataPelajaran;
            return [
                'nama'   => $mapel->nama,
                'harian' => $rows->where('jenis', 'harian')->avg('nilai'),
                'tugas'  => $rows->where('jenis', 'tugas')->avg('nilai'),
                'uts'    => $rows->where('jenis', 'uts')->first()?->nilai,
                'uas'    => $rows->where('jenis', 'uas')->first()?->nilai,
            ];
        })->values();

        $tahunAjarans = TahunAjaran::orderByDesc('is_aktif')->get();

        return view('ortu.nilai.index', compact(
            'siswa', 'nilaiGrouped', 'tahunAjarans', 'tahunAjaranId'
        ));
    }
}