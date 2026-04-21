<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\PembayaranSpp;
use Illuminate\Http\Request;

class PembayaranOrtuController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $orangTua = $user->orangTua;
        $siswa    = $orangTua?->siswa;

        $riwayat = $siswa
            ? PembayaranSpp::where('siswa_id', $siswa->id)->latest()->get()
            : collect();

        $bulanIni    = now()->format('Y-m');
        $sudahBayar  = $siswa?->isSppLunas();
        $menunggu    = $siswa
            ? PembayaranSpp::where('siswa_id', $siswa->id)
                ->where('periode', $bulanIni)
                ->where('status', 'menunggu')
                ->exists()
            : false;

        return view('ortu.spp.index', compact(
            'siswa', 'riwayat', 'bulanIni', 'sudahBayar', 'menunggu'
        ));
    }

    public function create()
    {
        $user     = auth()->user();
        $orangTua = $user->orangTua;
        $siswa    = $orangTua?->siswa;

        $bulanIni = now()->format('Y-m');

        // Cek apakah sudah ada pengajuan bulan ini
        $sudahAda = $siswa
            ? PembayaranSpp::where('siswa_id', $siswa->id)
                ->where('periode', $bulanIni)
                ->whereIn('status', ['menunggu', 'dikonfirmasi'])
                ->exists()
            : false;

        if ($sudahAda) {
            return redirect()->route('ortu.spp.index')
                ->with('info', 'Kamu sudah mengajukan pembayaran bulan ini.');
        }

        return view('ortu.spp.create', compact('siswa', 'bulanIni'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nominal'          => 'required|numeric|min:1000',
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'catatan_ortu'     => 'nullable|string|max:500',
        ]);

        $user     = auth()->user();
        $orangTua = $user->orangTua;
        $siswa    = $orangTua?->siswa;

        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $path = $request->file('bukti_pembayaran')
            ->store('spp/bukti', 'public');

        PembayaranSpp::create([
            'siswa_id'         => $siswa->id,
            'user_id'          => $user->id,
            'periode'          => now()->format('Y-m'),
            'nominal'          => $request->nominal,
            'bukti_pembayaran' => $path,
            'catatan_ortu'     => $request->catatan_ortu,
            'status'           => 'menunggu',
        ]);

        return redirect()->route('ortu.spp.index')
            ->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu konfirmasi admin.');
    }
}