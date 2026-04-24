<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;

class AbsensiOrtuController extends Controller
{
    public function index(Request $request)
    {
        $user  = auth()->user();
        $ortu  = $user->orangTua;
        $siswa = $ortu?->siswa;

        if (! $siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        $bulan = $request->get('bulan', now()->format('Y-m'));
        [$year, $month] = explode('-', $bulan);

        $absensis = Absensi::where('siswa_id', $siswa->id)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('tanggal')
            ->get();

        $rekap = [
            'hadir' => $absensis->where('status', 'hadir')->count(),
            'izin'  => $absensis->where('status', 'izin')->count(),
            'sakit' => $absensis->where('status', 'sakit')->count(),
            'alfa'  => $absensis->where('status', 'alfa')->count(),
        ];

        return view('ortu.absensi.index', compact('siswa', 'absensis', 'rekap', 'bulan'));
    }
}