@extends('layouts.app')
@section('title', 'Upload Bukti Pembayaran SPP')

@section('content')
<div class="mb-6">
    <a href="{{ route('ortu.spp.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h2 class="text-xl font-bold text-gray-800 dark:text-white mt-2">Upload Bukti Pembayaran SPP</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
        Periode: {{ \Carbon\Carbon::createFromFormat('Y-m', $bulanIni)->locale('id')->translatedFormat('F Y') }}
    </p>
</div>

<div class="max-w-lg">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">

        @if($siswa)
        <div class="flex items-center gap-3 mb-5 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                <span class="text-blue-600 dark:text-blue-400 font-semibold text-sm">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</span>
            </div>
            <div>
                <p class="font-medium text-gray-800 dark:text-white text-sm">{{ $siswa->nama_lengkap }}</p>
                <p class="text-xs text-gray-500">{{ $siswa->kelas->nama_kelas ?? '-' }} • NIS: {{ $siswa->nis }}</p>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('ortu.spp.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Nominal Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
                        <input type="number" name="nominal" value="{{ old('nominal') }}" required min="1000"
                            placeholder="0"
                            class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none @error('nominal') border-red-500 @enderror">
                    </div>
                    @error('nominal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Bukti Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                        <input type="file" name="bukti_pembayaran" id="bukti" required
                            accept=".jpg,.jpeg,.png,.pdf"
                            class="hidden"
                            onchange="document.getElementById('file-label').textContent = this.files[0]?.name || 'Pilih file'">
                        <label for="bukti" class="cursor-pointer">
                            <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p id="file-label" class="text-sm text-gray-600 dark:text-gray-400">Klik untuk upload atau drag & drop</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, PDF — Max 5MB</p>
                        </label>
                    </div>
                    @error('bukti_pembayaran')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Catatan (opsional)
                    </label>
                    <textarea name="catatan_ortu" rows="3"
                        placeholder="Misal: Transfer via BCA 1234567890"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">{{ old('catatan_ortu') }}</textarea>
                </div>

            </div>

            <div class="flex gap-3 mt-6 pt-5 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                    class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Kirim Bukti Pembayaran
                </button>
                <a href="{{ route('ortu.spp.index') }}"
                    class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                    Batal
                </a>
            </div>
        </form>

    </div>
</div>
@endsection