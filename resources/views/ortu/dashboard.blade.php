@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Assalamu'alaikum, {{ auth()->user()->name }}!</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
</div>

@if($siswa)
{{-- Info Siswa --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 mb-4">
    <div class="flex items-center gap-4">
        <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
            <span class="text-blue-600 dark:text-blue-400 text-xl font-bold">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</span>
        </div>
        <div>
            <p class="font-semibold text-gray-800 dark:text-white">{{ $siswa->nama_lengkap }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $siswa->kelas->nama_kelas ?? '-' }} • NIS: {{ $siswa->nis }}</p>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mt-1
                {{ $siswa->isSppLunas() ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                SPP {{ $siswa->isSppLunas() ? 'Lunas ✓' : 'Belum Bayar ✗' }}
            </span>
        </div>
    </div>
</div>

@if(!$siswa->isSppLunas())
<div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4 mb-4 flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-sm text-yellow-800 dark:text-yellow-200 font-medium">SPP bulan ini belum dibayar. Beberapa fitur mungkin terkunci.</p>
    </div>
    <a href="{{ route('ortu.spp.create') }}" class="flex-shrink-0 px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-medium rounded-lg transition-colors">Bayar</a>
</div>
@endif

@else
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-10 text-center">
    <p class="text-gray-500 dark:text-gray-400">Data siswa belum terdaftar. Hubungi admin sekolah.</p>
</div>
@endif
@endsection