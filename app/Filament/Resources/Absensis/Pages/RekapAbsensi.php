<?php

namespace App\Filament\Resources\Absensis\Pages;

use App\Filament\Resources\Absensis\AbsensiResource;
use App\Models\Absensi;
use App\Models\Kelas;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;

class RekapAbsensi extends Page
{
    protected static string $resource = AbsensiResource::class;
    protected string $view = 'filament.resources.absensis.pages.rekap-absensi';

    public ?string $kelas_id = null;
    public string  $bulan;
    public Collection $rekap;

    public function mount(): void
    {
        $this->bulan = now()->format('Y-m');
        $this->rekap = collect();

        $user = auth()->user();
        if ($user->hasRole('guru')) {
            $kelas = Kelas::where('wali_kelas_id', $user->id)->first();
            if ($kelas) {
                $this->kelas_id = (string) $kelas->id;
                $this->loadRekap();
            }
        }
    }

    public function getTitle(): string { return 'Rekap Absensi'; }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('input_absensi')
                ->label('Input Absensi')
                ->icon('heroicon-o-plus')
                ->url(AbsensiResource::getUrl('index')),
        ];
    }

    public function updatedKelasId(): void { $this->loadRekap(); }
    public function updatedBulan(): void   { $this->loadRekap(); }

    public function loadRekap(): void
    {
        if (! $this->kelas_id) {
            $this->rekap = collect();
            return;
        }

        [$year, $month] = explode('-', $this->bulan);

        $this->rekap = Absensi::with('siswa')
            ->where('kelas_id', $this->kelas_id)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get()
            ->groupBy('siswa_id')
            ->map(function ($rows) {
                $siswa = $rows->first()->siswa;
                return [
                    'nama'   => $siswa->nama,
                    'hadir'  => $rows->where('status', 'hadir')->count(),
                    'izin'   => $rows->where('status', 'izin')->count(),
                    'sakit'  => $rows->where('status', 'sakit')->count(),
                    'alfa'   => $rows->where('status', 'alfa')->count(),
                    'total'  => $rows->count(),
                ];
            })
            ->sortBy('nama')
            ->values();
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