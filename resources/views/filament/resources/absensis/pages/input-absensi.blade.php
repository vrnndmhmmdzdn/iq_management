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
                <x-filament::input.wrapper label="Tanggal">
                    <x-filament::input
                        type="date"
                        wire:model.live="tanggal"
                    />
                </x-filament::input.wrapper>
            </div>
        </div>
    </x-filament::section>

    @if($siswas->count() > 0)

        @if($sudah_diisi)
            <x-filament::section>
                <p class="text-sm text-warning-600 dark:text-warning-400">
                    ⚠️ Absensi tanggal ini sudah pernah diisi. Simpan ulang untuk memperbarui.
                </p>
            </x-filament::section>
        @endif

        <x-filament::section>
            <x-slot name="heading">
                Daftar Siswa — {{ $siswas->count() }} siswa
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm divide-y divide-gray-200 dark:divide-white/5">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-white/5">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Nama Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        @foreach($siswas as $i => $siswa)
                        <tr>
                            <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $siswa->nama_lengkap }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['hadir' => 'success', 'izin' => 'warning', 'sakit' => 'info', 'alfa' => 'danger'] as $status => $color)
                                    <label class="cursor-pointer">
                                        <input type="radio"
                                            wire:model.live="absensi_data.{{ $siswa->id }}.status"
                                            value="{{ $status }}"
                                            class="sr-only peer"
                                            id="status_{{ $siswa->id }}_{{ $status }}"
                                        />
                                        <x-filament::badge
                                            :color="$absensi_data[$siswa->id]['status'] === $status ? $color : 'gray'"
                                            class="cursor-pointer"
                                        >
                                            {{ ucfirst($status) }}
                                        </x-filament::badge>
                                    </label>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <x-filament::input.wrapper>
                                    <x-filament::input
                                        type="text"
                                        wire:model.blur="absensi_data.{{ $siswa->id }}.keterangan"
                                        placeholder="Opsional..."
                                    />
                                </x-filament::input.wrapper>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-slot name="footerActions">
                <div class="flex items-center justify-between w-full">
                    <div class="flex gap-4 text-sm text-gray-500">
                        <span>Hadir: <strong class="text-success-600">{{ collect($absensi_data)->where('status', 'hadir')->count() }}</strong></span>
                        <span>Izin: <strong class="text-warning-600">{{ collect($absensi_data)->where('status', 'izin')->count() }}</strong></span>
                        <span>Sakit: <strong class="text-info-600">{{ collect($absensi_data)->where('status', 'sakit')->count() }}</strong></span>
                        <span>Alfa: <strong class="text-danger-600">{{ collect($absensi_data)->where('status', 'alfa')->count() }}</strong></span>
                    </div>
                    <x-filament::button wire:click="simpan" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="simpan">Simpan Absensi</span>
                        <span wire:loading wire:target="simpan">Menyimpan...</span>
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::section>

    @elseif($kelas_id)
        <x-filament::section>
            <p class="text-center text-gray-400 py-8">Tidak ada siswa aktif di kelas ini.</p>
        </x-filament::section>
    @else
        <x-filament::section>
            <p class="text-center text-gray-400 py-8">Pilih kelas dan tanggal untuk mulai input absensi.</p>
        </x-filament::section>
    @endif

</x-filament-panels::page>