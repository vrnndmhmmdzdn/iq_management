<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Filter</x-slot>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <x-filament::input.wrapper label="Kelas">
                    <x-filament::input.select wire:model.live="kelas_id">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($this->getKelasOptions() as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
            <div>
                <x-filament::input.wrapper label="Bulan">
                    <x-filament::input
                        type="month"
                        wire:model.live="bulan"
                    />
                </x-filament::input.wrapper>
            </div>
        </div>
    </x-filament::section>

    @if($rekap->count() > 0)
        <x-filament::section>
            <x-slot name="heading">Rekap Kehadiran</x-slot>
            <div class="overflow-x-auto">
                <table class="w-full text-sm divide-y divide-gray-200 dark:divide-white/5">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-white/5">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Siswa</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-success-600 uppercase">Hadir</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-warning-600 uppercase">Izin</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-info-600 uppercase">Sakit</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-danger-600 uppercase">Alfa</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        @foreach($rekap as $row)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $row['nama'] }}</td>
                            <td class="px-4 py-3 text-center">
                                <x-filament::badge color="success">{{ $row['hadir'] }}</x-filament::badge>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-filament::badge color="warning">{{ $row['izin'] }}</x-filament::badge>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-filament::badge color="info">{{ $row['sakit'] }}</x-filament::badge>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-filament::badge color="danger">{{ $row['alfa'] }}</x-filament::badge>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $row['total'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @elseif($kelas_id)
        <x-filament::section>
            <p class="text-center text-gray-400 py-8">Belum ada data absensi untuk bulan ini.</p>
        </x-filament::section>
    @endif

</x-filament-panels::page>