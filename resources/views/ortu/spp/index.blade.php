@extends('layouts.app')
@section('title', 'Pembayaran SPP')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Pembayaran SPP</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Riwayat dan status pembayaran SPP</p>
</div>

{{-- Status Card --}}
<div class="rounded-xl p-5 mb-6 border
    {{ $sudahBayar ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : ($menunggu ? 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800') }}">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full flex items-center justify-center
                {{ $sudahBayar ? 'bg-green-100 dark:bg-green-800' : ($menunggu ? 'bg-yellow-100 dark:bg-yellow-800' : 'bg-red-100 dark:bg-red-800') }}">
                @if($sudahBayar)
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @elseif($menunggu)
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @else
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
            </div>
            <div>
                <p class="font-semibold
                    {{ $sudahBayar ? 'text-green-800 dark:text-green-200' : ($menunggu ? 'text-yellow-800 dark:text-yellow-200' : 'text-red-800 dark:text-red-200') }}">
                    @if($sudahBayar) SPP Bulan Ini Sudah Lunas ✓
                    @elseif($menunggu) Menunggu Konfirmasi Admin
                    @else SPP Bulan Ini Belum Dibayar
                    @endif
                </p>
                <p class="text-sm {{ $sudahBayar ? 'text-green-600 dark:text-green-400' : ($menunggu ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y') }}
                </p>
            </div>
        </div>
        @if(!$sudahBayar && !$menunggu)
        <a href="{{ route('ortu.spp.create') }}"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            Bayar Sekarang
        </a>
        @endif
    </div>
</div>

{{-- Riwayat --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-semibold text-gray-800 dark:text-white">Riwayat Pembayaran</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Periode</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Nominal</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Catatan Admin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($riwayat as $r)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-3 font-medium text-gray-800 dark:text-white">{{ $r->periode_label }}</td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">Rp {{ number_format($r->nominal, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $r->status === 'menunggu'     ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                            {{ $r->status === 'dikonfirmasi' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                            {{ $r->status === 'ditolak'      ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                        ">{{ $r->status_label }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $r->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $r->catatan_admin ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-gray-500 dark:text-gray-400">
                        Belum ada riwayat pembayaran.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection