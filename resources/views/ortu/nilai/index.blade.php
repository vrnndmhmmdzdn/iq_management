@extends('layouts.app')
@section('title', 'Nilai')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Rekap Nilai</h2>
    @if($siswa)
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
        {{ $siswa->nama_lengkap }} — {{ $siswa->kelas->nama_kelas ?? '-' }}
    </p>
    @endif
</div>

{{-- Filter Tahun Ajaran --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-5">
    <form method="GET" class="flex items-center gap-3">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Ajaran:</label>
        <select name="tahun_ajaran_id" onchange="this.form.submit()"
            class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
            @foreach($tahunAjarans as $ta)
            <option value="{{ $ta->id }}" {{ $ta->id == $tahunAjaranId ? 'selected' : '' }}>
                {{ $ta->nama }} {{ $ta->is_aktif ? '(Aktif)' : '' }}
            </option>
            @endforeach
        </select>
    </form>
</div>

@if($nilaiGrouped->isEmpty())
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada data nilai untuk tahun ajaran ini</p>
</div>
@else
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Mata Pelajaran</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 uppercase">Harian</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-yellow-600 uppercase">Tugas</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-purple-600 uppercase">UTS</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 uppercase">UAS</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($nilaiGrouped as $row)
            @php
                $nilaiColor = function($val) {
                    if ($val === null) return 'text-gray-400';
                    if ($val >= 80) return 'text-green-600 dark:text-green-400 font-semibold';
                    if ($val >= 60) return 'text-yellow-600 dark:text-yellow-400 font-semibold';
                    return 'text-red-600 dark:text-red-400 font-semibold';
                };
            @endphp
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $row['nama'] }}</td>
                <td class="px-4 py-3 text-center {{ $nilaiColor($row['harian']) }}">
                    {{ $row['harian'] !== null ? number_format($row['harian'], 1) : '-' }}
                </td>
                <td class="px-4 py-3 text-center {{ $nilaiColor($row['tugas']) }}">
                    {{ $row['tugas'] !== null ? number_format($row['tugas'], 1) : '-' }}
                </td>
                <td class="px-4 py-3 text-center {{ $nilaiColor($row['uts']) }}">
                    {{ $row['uts'] !== null ? number_format($row['uts'], 1) : '-' }}
                </td>
                <td class="px-4 py-3 text-center {{ $nilaiColor($row['uas']) }}">
                    {{ $row['uas'] !== null ? number_format($row['uas'], 1) : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection