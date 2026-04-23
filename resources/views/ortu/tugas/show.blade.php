@extends('layouts.app')
@section('title', $tugas->judul)

@section('content')
<div class="mb-6">
    <a href="{{ route('ortu.tugas.index', ['tanggal' => $tugas->tanggal->format('Y-m-d')]) }}"
        class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Papan Tugas
    </a>
</div>

<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

        {{-- Header --}}
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                @if($tugas->mataPelajaran)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                    {{ $tugas->mataPelajaran->nama }}
                </span>
                @endif
                @if($tugas->kelas)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                    {{ $tugas->kelas->nama_kelas }}
                </span>
                @endif
                @if($tugas->isTerlambat())
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                    Waktu habis
                </span>
                @endif
            </div>
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ $tugas->judul }}</h1>

            <div class="flex items-center gap-4 mt-3 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $tugas->tanggal->locale('id')->translatedFormat('l, d F Y') }}
                </div>
                @if($tugas->batas_waktu)
                <div class="flex items-center gap-1.5 {{ $tugas->isTerlambat() ? 'text-red-500' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Batas: {{ \Carbon\Carbon::parse($tugas->batas_waktu)->format('H:i') }} WIB
                </div>
                @endif
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ $tugas->createdBy->name }}
                </div>
            </div>
        </div>

        {{-- Isi Tugas --}}
        <div class="p-6">
            <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Deskripsi Tugas</h2>
            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">
                {{ $tugas->deskripsi }}
            </div>
        </div>

        {{-- Lampiran --}}
        @if($tugas->file_lampiran)
        <div class="px-6 pb-6">
            <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">File Lampiran</h2>
            @php $ext = pathinfo($tugas->file_lampiran, PATHINFO_EXTENSION); @endphp

            @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp']))
            <img src="{{ Storage::url($tugas->file_lampiran) }}" alt="Lampiran"
                class="w-full rounded-lg border border-gray-200 dark:border-gray-700 max-w-md">
            @else
            <a href="{{ Storage::url($tugas->file_lampiran) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download Lampiran ({{ strtoupper($ext) }})
            </a>
            @endif
        </div>
        @endif

    </div>
</div>
@endsection