<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with(['waliKelas', 'tahunAjaran'])
            ->withCount('siswa')
            ->latest()
            ->paginate(15);

        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        $tahunAjaran = TahunAjaran::all();
        $guru        = User::role('guru')->get();
        return view('admin.kelas.create', compact('tahunAjaran', 'guru'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas'      => 'required|string|max:50',
            'tingkat'         => 'required|integer|min:1|max:6',
            'wali_kelas_id'   => 'nullable|exists:users,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
        ]);

        Kelas::create($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kela)
    {
        $kela->load(['waliKelas', 'tahunAjaran', 'siswa']);
        return view('admin.kelas.show', compact('kela'));
    }

    public function edit(Kelas $kela)
    {
        $tahunAjaran = TahunAjaran::all();
        $guru        = User::role('guru')->get();
        return view('admin.kelas.edit', compact('kela', 'tahunAjaran', 'guru'));
    }

    public function update(Request $request, Kelas $kela)
    {
        $validated = $request->validate([
            'nama_kelas'      => 'required|string|max:50',
            'tingkat'         => 'required|integer|min:1|max:6',
            'wali_kelas_id'   => 'nullable|exists:users,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
        ]);

        $kela->update($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil diupdate.');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}