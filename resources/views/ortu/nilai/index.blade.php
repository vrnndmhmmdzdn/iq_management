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

{{-- Filter --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-5">
    <form method="GET" class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Tahun Ajaran:</label>
            <select name="tahun_ajaran_id" onchange="this.form.submit()"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                @foreach($tahunAjarans as $ta)
                <option value="{{ $ta->id }}" {{ $ta->id == $tahunAjaranId ? 'selected' : '' }}>
                    {{ $ta->nama }} {{ $ta->is_aktif ? '(Aktif)' : '' }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Mata Pelajaran:</label>
            <select name="mapel_filter" onchange="this.form.submit()"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                <option value="">Semua</option>
                @foreach($mapelList as $mapel)
                <option value="{{ $mapel->id }}" {{ $mapel->id == $mapelFilter ? 'selected' : '' }}>
                    {{ $mapel->nama }}
                </option>
                @endforeach
            </select>
        </div>
        <input type="hidden" name="tab" value="{{ $activeTab }}" id="tab-input">
    </form>
</div>

{{-- Tabs --}}
<div class="flex border-b border-gray-200 dark:border-gray-700 mb-4">
    @foreach([
        'harian' => ['label' => 'Harian', 'color' => 'blue'],
        'tugas'  => ['label' => 'Tugas',  'color' => 'yellow'],
        'uts'    => ['label' => 'UTS',    'color' => 'purple'],
        'uas'    => ['label' => 'UAS',    'color' => 'green'],
    ] as $key => $item)
    <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
        class="flex-1 text-center py-3 text-sm font-medium transition-colors border-b-2
            {{ $activeTab === $key
                ? match($item['color']) {
                    'blue'   => 'border-blue-500 text-blue-600 dark:text-blue-400',
                    'yellow' => 'border-yellow-500 text-yellow-600 dark:text-yellow-400',
                    'purple' => 'border-purple-500 text-purple-600 dark:text-purple-400',
                    'green'  => 'border-green-500 text-green-600 dark:text-green-400',
                }
                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
        {{ $item['label'] }}
    </a>
    @endforeach
</div>

{{-- Tabel Nilai --}}
@if($nilaiTabed->isEmpty())
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
    <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada data</p>
</div>
@else
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Mata Pelajaran</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Nilai Kamu</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Rata-rata Kelas</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Keterangan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($nilaiTabed as $row)
            @php
                $color = match(true) {
                    $row->nilai === null => 'text-gray-400 dark:text-gray-500',
                    $row->nilai >= 80    => 'text-green-600 dark:text-green-400',
                    $row->nilai >= 60    => 'text-yellow-600 dark:text-yellow-400',
                    default              => 'text-red-600 dark:text-red-400',
                };
            @endphp
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-900 dark:text-white">{{ $row->mapel_nama }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $row->guru_nama }}</p>
                </td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                    {{ $row->tanggal_ujian }}
                </td>
                <td class="px-4 py-3 text-center font-semibold {{ $color }}">
                    {{ $row->nilai !== null ? number_format($row->nilai, 1) : '—' }}
                </td>
                <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 font-medium">
                    {{ $row->rata_rata_kelas !== '-' ? number_format($row->rata_rata_kelas, 1) : '—' }}
                </td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                    {{ $row->keterangan ?? '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection