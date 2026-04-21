<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembayaranSpp;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = PembayaranSpp::with(['siswa.kelas', 'user'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->periode) {
            $query->where('periode', $request->periode);
        }

        $pembayaran = $query->paginate(15);

        return view('admin.pembayaran.index', compact('pembayaran'));
    }

    public function show(PembayaranSpp $pembayaran)
    {
        $pembayaran->load(['siswa.kelas', 'user', 'dikonfirmasiOleh']);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function konfirmasi(Request $request, PembayaranSpp $pembayaran)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $pembayaran->update([
            'status'           => 'dikonfirmasi',
            'catatan_admin'    => $request->catatan_admin,
            'dikonfirmasi_oleh'=> auth()->id(),
            'dikonfirmasi_at'  => now(),
        ]);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function tolak(Request $request, PembayaranSpp $pembayaran)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ]);

        $pembayaran->update([
            'status'           => 'ditolak',
            'catatan_admin'    => $request->catatan_admin,
            'dikonfirmasi_oleh'=> auth()->id(),
            'dikonfirmasi_at'  => now(),
        ]);

        return back()->with('success', 'Pembayaran ditolak.');
    }

    public function tunggakan()
    {
        $bulanIni = now()->format('Y-m');

        $tunggakan = Siswa::with(['kelas', 'orangTua'])
            ->where('status', 'aktif')
            ->whereDoesntHave('pembayaranSpp', function ($q) use ($bulanIni) {
                $q->where('periode', $bulanIni)
                  ->where('status', 'dikonfirmasi');
            })
            ->get();

        return view('admin.pembayaran.tunggakan', compact('tunggakan', 'bulanIni'));
    }
}