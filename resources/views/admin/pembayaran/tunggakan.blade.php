@extends('layouts.app')
@section('title', 'Rekap Tunggakan SPP')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Rekap Tunggakan SPP</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
            Siswa yang belum melunasi SPP bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $bulanIni)->locale('id')->translatedFormat('F Y') }}
        </p>
    </div>
    <span class="inline-flex items-center px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 text-sm font-semibold rounded-lg">
        {{ $tunggakan->count() }} siswa belum bayar
    </span>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">No</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Nama Siswa</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">NIS</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Kelas</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Wali</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($tunggakan as $i => $s)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-3 text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-5 py-3 font-medium text-gray-800 dark:text-white">{{ $s->nama_lengkap }}</td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $s->nis }}</td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                        {{ $s->orangTua->first()?->nama ?? '-' }}
                        @if($s->orangTua->first()?->no_hp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $s->orangTua->first()->no_hp) }}" target="_blank"
                                class="ml-2 text-green-600 hover:underline text-xs">WA</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-gray-500 dark:text-gray-400">
                        Semua siswa sudah membayar SPP bulan ini! 🎉
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection