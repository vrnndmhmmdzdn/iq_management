<?php

namespace App\Filament\Resources\Absensis\Pages;

use App\Filament\Resources\Absensis\AbsensiResource;
use App\Filament\Resources\Absensis\Tables\DetailAbsensisTable;
use App\Models\Absensi;
use App\Models\Kelas;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DetailAbsensiKelas extends ListRecords
{
    protected static string $resource = AbsensiResource::class;

    public string $kelas   = '';
    public string $tanggal = '';

    public function mount(): void
    {
        $this->kelas   = request()->route('kelas');
        $this->tanggal = request()->route('tanggal');
    }

    public function getTitle(): string
    {
        $kelas = Kelas::find($this->kelas);
        $tgl   = \Carbon\Carbon::parse($this->tanggal)
            ->locale('id')
            ->translatedFormat('l, d F Y');
        return "Absensi {$kelas?->nama_kelas} — {$tgl}";
    }

    public function table(Table $table): Table
    {
        return DetailAbsensisTable::configure($table);
    }

    protected function getTableQuery(): Builder
    {
        return Absensi::query()
            ->where('kelas_id', $this->kelas)
            ->whereDate('tanggal', $this->tanggal)
            ->with(['siswa', 'pencatat']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('hapus_absensi_kelas')
                ->label('Hapus Absensi Kelas Ini')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Hapus Semua Absensi')
                ->modalDescription('Semua data absensi kelas ini pada tanggal ini akan dihapus permanen. Lanjutkan?')
                ->action(function () {
                    $deleted = Absensi::where('kelas_id', $this->kelas)
                        ->whereDate('tanggal', $this->tanggal)
                        ->delete();

                    Notification::make()
                        ->title("{$deleted} data absensi berhasil dihapus")
                        ->success()
                        ->send();

                    $this->redirect(AbsensiResource::getUrl('index'));
                }),

            Action::make('kembali')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(AbsensiResource::getUrl('index')),
        ];
    }
}