@extends('layouts.app')
@section('title', 'Detail Siswa')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.siswa.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Data Siswa
    </a>
    <div class="flex items-center justify-between mt-2">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Detail Siswa</h2>
        <a href="{{ route('admin.siswa.edit', $siswa) }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Data
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Profil Siswa --}}
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 text-center">
            @if($siswa->foto)
                <img src="{{ Storage::url($siswa->foto) }}" alt="{{ $siswa->nama_lengkap }}"
                    class="w-24 h-24 rounded-full object-cover mx-auto mb-3 border-4 border-blue-100 dark:border-blue-900">
            @else
                <div class="w-24 h-24 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-3">
                    <span class="text-blue-600 dark:text-blue-400 text-3xl font-bold">
                        {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                    </span>
                </div>
            @endif
            <h3 class="font-bold text-gray-800 dark:text-white text-lg">{{ $siswa->nama_lengkap }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</p>
            <div class="flex items-center justify-center gap-2 mt-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $siswa->status === 'aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                    {{ ucfirst($siswa->status) }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $siswa->isSppLunas() ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                    SPP {{ $siswa->isSppLunas() ? 'Lunas' : 'Belum' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Info Detail --}}
    <div class="lg:col-span-2 space-y-4">

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Data Pribadi</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">NIS</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $siswa->nis }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">NISN</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $siswa->nisn ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Jenis Kelamin</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Tanggal Lahir</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">
                        {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d M Y') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Tempat Lahir</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $siswa->tempat_lahir ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Tahun Ajaran</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $siswa->tahunAjaran->nama ?? '-' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-gray-500 dark:text-gray-400">Alamat</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $siswa->alamat ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Data Orang Tua --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Data Orang Tua / Wali</h3>
            @forelse($siswa->orangTua as $ot)
            <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-purple-600 dark:text-purple-400 text-xs font-semibold">{{ strtoupper(substr($ot->nama, 0, 1)) }}</span>
                </div>
                <div class="flex-1 text-sm">
                    <p class="font-medium text-gray-800 dark:text-white">{{ $ot->nama }}</p>
                    <p class="text-gray-500 dark:text-gray-400">{{ ucfirst($ot->hubungan) }} • {{ $ot->pekerjaan ?? '-' }}</p>
                    @if($ot->no_hp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ot->no_hp) }}" target="_blank"
                        class="text-green-600 hover:underline text-xs">{{ $ot->no_hp }}</a>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data orang tua.</p>
            @endforelse
        </div>

        {{-- Riwayat SPP --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Riwayat Pembayaran SPP</h3>
            @forelse($siswa->pembayaranSpp->take(6) as $spp)
            <div class="flex items-center justify-between py-2.5 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }} text-sm">
                <span class="text-gray-700 dark:text-gray-300">{{ $spp->periode_label }}</span>
                <div class="flex items-center gap-3">
                    <span class="font-medium text-gray-800 dark:text-white">Rp {{ number_format($spp->nominal, 0, ',', '.') }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $spp->status === 'dikonfirmasi' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                        {{ $spp->status === 'menunggu'     ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                        {{ $spp->status === 'ditolak'      ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                    ">{{ $spp->status_label }}</span>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat pembayaran.</p>
            @endforelse
        </div>

    </div>
</div>
@endsection