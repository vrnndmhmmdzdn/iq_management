@extends('layouts.app')
@section('title', 'Absensi Harian')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Absensi Harian</h2>
    @if($siswa)
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
        {{ $siswa->nama_lengkap }} — {{ $siswa->kelas->nama_kelas ?? '-' }}
    </p>
    @endif
</div>

{{-- Filter Bulan --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-4">
    <form method="GET" class="flex items-center gap-3">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Bulan:</label>
        <input type="month" name="bulan" value="{{ $bulan }}"
            class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
            onchange="this.form.submit()" />
    </form>
</div>

{{-- Kartu Ringkasan --}}
<div class="grid grid-cols-4 gap-3 mb-5">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-green-200 dark:border-green-800 p-4 text-center">
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $rekap['hadir'] }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hadir</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-yellow-200 dark:border-yellow-800 p-4 text-center">
        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $rekap['izin'] }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Izin</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-blue-200 dark:border-blue-800 p-4 text-center">
        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $rekap['sakit'] }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sakit</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-red-200 dark:border-red-800 p-4 text-center">
        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $rekap['alfa'] }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Alfa</p>
    </div>
</div>

{{-- Detail Per Hari --}}
@if($absensis->isEmpty())
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada data absensi untuk bulan ini</p>
</div>
@else
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Keterangan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($absensis as $absen)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                    {{ $absen->tanggal->locale('id')->translatedFormat('l, d F Y') }}
                </td>
                <td class="px-4 py-3">
                    @php
                        $badge = match($absen->status) {
                            'hadir' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                            'izin'  => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                            'sakit' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                            'alfa'  => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            default => 'bg-gray-100 text-gray-700',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                        {{ $absen->status_label }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                    {{ $absen->keterangan ?? '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection