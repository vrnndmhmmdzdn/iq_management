<?php

namespace App\Filament\Resources\Absensis\Pages;

use App\Filament\Resources\Absensis\AbsensiResource;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;

class InputAbsensi extends Page
{
    protected static string $resource = AbsensiResource::class;
    protected string $view = 'filament.resources.absensis.pages.input-absensi';

    public ?string $kelas_id  = null;
    public string  $tanggal;
    public array   $absensi_data = [];
    public bool    $sudah_diisi  = false;

    /** @var Collection<Siswa> */
    public Collection $siswas;

    public function mount(): void
    {
        $this->tanggal = today()->format('Y-m-d');
        $this->siswas  = collect();

        // Guru langsung load kelas wali kelasnya
        $user = auth()->user();
        if ($user->hasRole('guru')) {
            $kelas = Kelas::where('wali_kelas_id', $user->id)->first();
            if ($kelas) {
                $this->kelas_id = (string) $kelas->id;
                $this->loadSiswa();
            }
        }
    }

    public function getTitle(): string
    {
        return 'Input Absensi Harian';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lihat_rekap')
                ->label('Lihat Rekap')
                ->icon('heroicon-o-table-cells')
                ->url(AbsensiResource::getUrl('rekap')),
        ];
    }

    public function updatedKelasId(): void
    {
        $this->loadSiswa();
    }

    public function updatedTanggal(): void
    {
        $this->loadSiswa();
    }

    public function loadSiswa(): void
    {
        if (! $this->kelas_id) {
            $this->siswas = collect();
            return;
        }

        $this->siswas = Siswa::where('kelas_id', $this->kelas_id)
            ->whereNull('deleted_at')
            ->orderBy('nama')
            ->get();

        $existing = Absensi::where('kelas_id', $this->kelas_id)
            ->whereDate('tanggal', $this->tanggal)
            ->get()
            ->keyBy('siswa_id');

        $this->sudah_diisi = $existing->isNotEmpty();

        $this->absensi_data = [];
        foreach ($this->siswas as $siswa) {
            $this->absensi_data[$siswa->id] = [
                'status'     => $existing->get($siswa->id)?->status ?? 'hadir',
                'keterangan' => $existing->get($siswa->id)?->keterangan ?? '',
            ];
        }
    }

    public function simpan(): void
    {
        if (! $this->kelas_id || $this->siswas->isEmpty()) {
            Notification::make()
                ->title('Pilih kelas dan tanggal terlebih dahulu')
                ->warning()->send();
            return;
        }

        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();

        foreach ($this->absensi_data as $siswaId => $data) {
            Absensi::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'tanggal'  => $this->tanggal,
                ],
                [
                    'kelas_id'         => $this->kelas_id,
                    'tahun_ajaran_id'  => $tahunAjaran?->id,
                    'status'           => $data['status'],
                    'keterangan'       => $data['keterangan'] ?? null,
                    'dicatat_oleh'     => auth()->id(),
                ]
            );
        }

        Notification::make()
            ->title('Absensi berhasil disimpan!')
            ->success()->send();

        $this->sudah_diisi = true;
    }

    public function getKelasOptions(): array
    {
        $user  = auth()->user();
        $query = Kelas::query();

        if ($user->hasRole('guru')) {
            $query->where('wali_kelas_id', $user->id);
        }

        return $query->orderBy('nama_kelas')->pluck('nama_kelas', 'id')->toArray();
    }
}