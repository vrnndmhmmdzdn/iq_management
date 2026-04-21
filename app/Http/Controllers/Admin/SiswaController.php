<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Siswa::with(['kelas', 'tahunAjaran'])->latest()->paginate(15);
        return view('admin.siswa.index', compact('siswa'));
    }

    public function create()
    {
        $kelas       = Kelas::all();
        $tahunAjaran = TahunAjaran::all();
        return view('admin.siswa.create', compact('kelas', 'tahunAjaran'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis'           => 'required|unique:siswas',
            'nisn'          => 'nullable|unique:siswas',
            'nama_lengkap'  => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir'  => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string',
            'kelas_id'      => 'nullable|exists:kelas,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajarans,id',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('siswa/foto', 'public');
        }

        Siswa::create($validated);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa)
    {
        $siswa->load(['kelas', 'tahunAjaran', 'orangTua', 'pembayaranSpp']);
        return view('admin.siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa)
    {
        $kelas       = Kelas::all();
        $tahunAjaran = TahunAjaran::all();
        return view('admin.siswa.edit', compact('siswa', 'kelas', 'tahunAjaran'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nis'           => 'required|unique:siswas,nis,'.$siswa->id,
            'nisn'          => 'nullable|unique:siswas,nisn,'.$siswa->id,
            'nama_lengkap'  => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir'  => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string',
            'kelas_id'      => 'nullable|exists:kelas,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajarans,id',
            'status'        => 'required|in:aktif,nonaktif,lulus',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('siswa/foto', 'public');
        }

        $siswa->update($validated);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diupdate.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return back()->with('success', 'Data siswa berhasil dihapus.');
    }
}