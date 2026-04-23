<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use Illuminate\Http\Request;

class TugasOrtuController extends Controller
{
    public function index(Request $request)
    {
        $user     = auth()->user();
        $orangTua = $user->orangTua;
        $siswa    = $orangTua?->siswa;

        // Filter tugas berdasarkan kelas siswa
        $tanggal = $request->tanggal ?? today()->format('Y-m-d');

        $tugas = Tugas::with(['mataPelajaran', 'kelas', 'createdBy'])
            ->where('is_aktif', true)
            ->whereDate('tanggal', $tanggal)
            ->where(function ($q) use ($siswa) {
                $q->whereNull('kelas_id') // tugas untuk semua kelas
                  ->orWhere('kelas_id', $siswa?->kelas_id); // atau kelas siswa
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Navigasi hari
        $hariIni    = today();
        $tanggalObj = \Carbon\Carbon::parse($tanggal);
        $kemarin    = $tanggalObj->copy()->subDay()->format('Y-m-d');
        $besok      = $tanggalObj->copy()->addDay()->format('Y-m-d');
        $isHariIni  = $tanggalObj->isToday();

        return view('ortu.tugas.index', compact(
            'tugas', 'siswa', 'tanggal', 'tanggalObj',
            'kemarin', 'besok', 'isHariIni'
        ));
    }

    public function show(Tugas $tugas)
    {
        $user     = auth()->user();
        $orangTua = $user->orangTua;
        $siswa    = $orangTua?->siswa;

        // Pastikan tugas untuk kelas siswa atau semua kelas
        if ($tugas->kelas_id && $tugas->kelas_id !== $siswa?->kelas_id) {
            abort(403, 'Tugas ini bukan untuk kelas anak Anda.');
        }

        $tugas->load(['mataPelajaran', 'kelas', 'createdBy']);

        return view('ortu.tugas.show', compact('tugas', 'siswa'));
    }
}