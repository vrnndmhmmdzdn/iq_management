@extends('layouts.app')
@section('title', 'Detail Pembayaran SPP')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.pembayaran.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h2 class="text-xl font-bold text-gray-800 dark:text-white mt-2">Detail Pengajuan SPP</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Info Pembayaran --}}
    <div class="lg:col-span-2 space-y-4">

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Informasi Siswa</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Nama Siswa</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $pembayaran->siswa->nama_lengkap }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">NIS</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $pembayaran->siswa->nis }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Kelas</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Diajukan Oleh</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $pembayaran->user->name }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Detail Pembayaran</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Periode</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $pembayaran->periode_label }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Nominal</p>
                    <p class="font-semibold text-xl text-gray-800 dark:text-white mt-0.5">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-0.5
                        {{ $pembayaran->status === 'menunggu'     ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                        {{ $pembayaran->status === 'dikonfirmasi' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                        {{ $pembayaran->status === 'ditolak'      ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                    ">{{ $pembayaran->status_label }}</span>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Tanggal Pengajuan</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $pembayaran->created_at->format('d M Y H:i') }}</p>
                </div>
                @if($pembayaran->catatan_ortu)
                <div class="col-span-2">
                    <p class="text-gray-500 dark:text-gray-400">Catatan Orang Tua</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $pembayaran->catatan_ortu }}</p>
                </div>
                @endif
                @if($pembayaran->catatan_admin)
                <div class="col-span-2">
                    <p class="text-gray-500 dark:text-gray-400">Catatan Admin</p>
                    <p class="font-medium text-gray-800 dark:text-white mt-0.5">{{ $pembayaran->catatan_admin }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Bukti Pembayaran --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Bukti Pembayaran</h3>
            @php $ext = pathinfo($pembayaran->bukti_pembayaran, PATHINFO_EXTENSION); @endphp
            @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp']))
                <img src="{{ Storage::url($pembayaran->bukti_pembayaran) }}"
                    alt="Bukti Pembayaran"
                    class="w-full max-w-md rounded-lg border border-gray-200 dark:border-gray-700">
            @else
                <a href="{{ Storage::url($pembayaran->bukti_pembayaran) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 text-sm rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Lihat File PDF
                </a>
            @endif
        </div>
    </div>

    {{-- Action Panel --}}
    @if($pembayaran->status === 'menunggu')
    <div class="space-y-4">

        {{-- Konfirmasi --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-semibold text-green-600 dark:text-green-400 mb-3">✓ Konfirmasi Pembayaran</h3>
            <form method="POST" action="{{ route('admin.pembayaran.konfirmasi', $pembayaran) }}">
                @csrf @method('PATCH')
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Catatan (opsional)</label>
                    <textarea name="catatan_admin" rows="3" placeholder="Misal: Pembayaran diterima via BCA"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-green-500 outline-none"></textarea>
                </div>
                <button type="submit"
                    onclick="return confirm('Konfirmasi pembayaran ini sebagai LUNAS?')"
                    class="w-full py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Konfirmasi Lunas
                </button>
            </form>
        </div>

        {{-- Tolak --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-semibold text-red-600 dark:text-red-400 mb-3">✗ Tolak Pembayaran</h3>
            <form method="POST" action="{{ route('admin.pembayaran.tolak', $pembayaran) }}">
                @csrf @method('PATCH')
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="catatan_admin" rows="3" required placeholder="Misal: Bukti pembayaran tidak jelas"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-red-500 outline-none"></textarea>
                </div>
                <button type="submit"
                    onclick="return confirm('Yakin menolak pembayaran ini?')"
                    class="w-full py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Tolak Pembayaran
                </button>
            </form>
        </div>

    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Info Konfirmasi</h3>
        <div class="text-sm space-y-2">
            <p class="text-gray-500">Dikonfirmasi oleh: <span class="font-medium text-gray-800 dark:text-white">{{ $pembayaran->dikonfirmasiOleh->name ?? '-' }}</span></p>
            <p class="text-gray-500">Pada: <span class="font-medium text-gray-800 dark:text-white">{{ $pembayaran->dikonfirmasi_at?->format('d M Y H:i') ?? '-' }}</span></p>
        </div>
    </div>
    @endif

</div>
@endsection