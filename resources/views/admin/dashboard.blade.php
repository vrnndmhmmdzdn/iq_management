@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
@php
    $totalSiswa    = \App\Models\Siswa::where('status','aktif')->count();
    $totalGuru     = \App\Models\User::role('guru')->count();
    $totalOrtu     = \App\Models\User::role('ortu')->count();
    $menungguSpp   = \App\Models\PembayaranSpp::where('status','menunggu')->count();
    $bulanIni      = now()->format('Y-m');
    $lunasbulanIni = \App\Models\PembayaranSpp::where('periode',$bulanIni)->where('status','dikonfirmasi')->count();
    $pengajuanTerbaru = \App\Models\PembayaranSpp::with(['siswa','user'])->where('status','menunggu')->latest()->take(5)->get();
@endphp

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Siswa Aktif</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalSiswa }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Guru</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalGuru }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">SPP Menunggu</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $menungguSpp }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">SPP Lunas Bulan Ini</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $lunasbulanIni }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

</div>

{{-- Pengajuan SPP Terbaru --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="font-semibold text-gray-800 dark:text-white">Pengajuan SPP Terbaru</h2>
        <a href="{{ route('admin.pembayaran.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Lihat semua</a>
    </div>
    <div class="overflow-x-auto">
        @if($pengajuanTerbaru->isEmpty())
            <div class="px-5 py-10 text-center text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Tidak ada pengajuan SPP yang menunggu konfirmasi.
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Siswa</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Periode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Nominal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($pengajuanTerbaru as $p)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800 dark:text-white">{{ $p->siswa->nama_lengkap ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $p->siswa->nis ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $p->periode_label }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $p->created_at->diffForHumans() }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.pembayaran.show', $p) }}"
                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                                Review
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection