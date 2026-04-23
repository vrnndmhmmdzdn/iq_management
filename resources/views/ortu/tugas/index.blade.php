@extends('layouts.app')
@section('title', 'Tugas Harian')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Papan Tugas Harian</h2>
    @if($siswa)
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
        {{ $siswa->nama_lengkap }} — {{ $siswa->kelas->nama_kelas ?? '-' }}
    </p>
    @endif
</div>

{{-- Navigasi Tanggal --}}
<div class="flex items-center justify-between mb-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-3">
    <a href="{{ route('ortu.tugas.index', ['tanggal' => $kemarin]) }}"
        class="flex items-center gap-1.5 px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kemarin
    </a>

    <div class="text-center">
        <p class="font-semibold text-gray-800 dark:text-white">
            {{ $tanggalObj->locale('id')->translatedFormat('l') }}
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ $tanggalObj->locale('id')->translatedFormat('d F Y') }}
        </p>
        @if($isHariIni)
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 mt-1">
            Hari Ini
        </span>
        @endif
    </div>

    @if(!$isHariIni)
    <a href="{{ route('ortu.tugas.index', ['tanggal' => $besok]) }}"
        class="flex items-center gap-1.5 px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
        Besok
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
    @else
    <div class="w-24"></div>
    @endif
</div>

{{-- List Tugas --}}
@if($tugas->isEmpty())
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada tugas untuk hari ini</p>
    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Cek kembali besok atau lihat hari lain</p>
</div>
@else
<div class="space-y-3">
    @foreach($tugas as $t)
    <a href="{{ route('ortu.tugas.show', $t) }}"
        class="block bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md hover:border-blue-300 dark:hover:border-blue-700 transition-all">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    @if($t->mataPelajaran)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                        {{ $t->mataPelajaran->nama }}
                    </span>
                    @endif
                    @if($t->kelas)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                        {{ $t->kelas->nama_kelas }}
                    </span>
                    @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        Semua Kelas
                    </span>
                    @endif
                    @if($t->isTerlambat())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                        Sudah lewat batas waktu
                    </span>
                    @endif
                </div>

                <h3 class="font-semibold text-gray-800 dark:text-white text-base">{{ $t->judul }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $t->deskripsi }}</p>
            </div>

            <div class="text-right flex-shrink-0">
                @if($t->batas_waktu)
                <p class="text-xs text-gray-500 dark:text-gray-400">Batas waktu</p>
                <p class="text-sm font-semibold {{ $t->isTerlambat() ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-white' }}">
                    {{ \Carbon\Carbon::parse($t->batas_waktu)->format('H:i') }} WIB
                </p>
                @endif
                @if($t->file_lampiran)
                <div class="mt-2">
                    <span class="inline-flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        Ada lampiran
                    </span>
                </div>
                @endif
                <svg class="w-5 h-5 text-gray-400 mt-2 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>

        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center gap-2 text-xs text-gray-400">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Diberikan oleh {{ $t->createdBy->name }}
        </div>
    </a>
    @endforeach
</div>
@endif
@endsection