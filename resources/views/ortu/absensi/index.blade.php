<x-app-layout>
    <div class="py-6 px-4 max-w-3xl mx-auto space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Absensi Harian</h1>
            <p class="text-sm text-gray-500 mt-1">Rekap kehadiran {{ $siswa->nama }}</p>
        </div>

        {{-- Filter Bulan --}}
        <form method="GET" class="flex items-center gap-3">
            <input type="month" name="bulan" value="{{ $bulan }}"
                class="rounded-lg border-gray-300 text-sm"
                onchange="this.form.submit()" />
        </form>

        {{-- Kartu Ringkasan --}}
        <div class="grid grid-cols-4 gap-3">
            <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $rekap['hadir'] }}</p>
                <p class="text-xs text-green-500 mt-1">Hadir</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-yellow-600">{{ $rekap['izin'] }}</p>
                <p class="text-xs text-yellow-500 mt-1">Izin</p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $rekap['sakit'] }}</p>
                <p class="text-xs text-blue-500 mt-1">Sakit</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-red-600">{{ $rekap['alfa'] }}</p>
                <p class="text-xs text-red-500 mt-1">Alfa</p>
            </div>
        </div>

        {{-- Detail Per Hari --}}
        @if($absensis->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($absensis as $absen)
                    <tr>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $absen->tanggal->translatedFormat('l, d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $colorMap = ['hadir'=>'green','izin'=>'yellow','sakit'=>'blue','alfa'=>'red'];
                                $c = $colorMap[$absen->status] ?? 'gray';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                @if($absen->status === 'hadir') bg-green-100 text-green-700
                                @elseif($absen->status === 'izin') bg-yellow-100 text-yellow-700
                                @elseif($absen->status === 'sakit') bg-blue-100 text-blue-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $absen->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $absen->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12 text-gray-400">
            <p>Belum ada data absensi untuk bulan ini.</p>
        </div>
        @endif
    </div>
</x-app-layout>