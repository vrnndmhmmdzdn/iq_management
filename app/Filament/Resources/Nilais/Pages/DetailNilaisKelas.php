<?php

namespace App\Filament\Resources\Nilais\Pages;

use App\Filament\Resources\Nilais\NilaiResource;
use App\Filament\Resources\Nilais\Tables\DetailNilaisTable;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DetailNilaiKelas extends ListRecords
{
    protected static string $resource = NilaiResource::class;

    public string $kelas = '';
    public string $mapel = '';
    public string $jenis = '';

    public function mount(): void
    {
        $this->kelas = request()->route('kelas');
        $this->mapel = request()->route('mapel');
        $this->jenis = request()->route('jenis');
    }

    public function getTitle(): string
    {
        $kelas     = Kelas::find($this->kelas);
        $mapel     = MataPelajaran::find($this->mapel);
        $jenisLabel = match($this->jenis) {
            'harian' => 'Harian',
            'tugas'  => 'Tugas',
            'uts'    => 'UTS',
            'uas'    => 'UAS',
            default  => '-',
        };
        return "Nilai {$jenisLabel} — {$mapel?->nama} — {$kelas?->nama_kelas}";
    }

    public function table(Table $table): Table
    {
        return DetailNilaisTable::configure($table);
    }

    protected function getTableQuery(): Builder
    {
        return Nilai::query()
            ->where('kelas_id', $this->kelas)
            ->where('mata_pelajaran_id', $this->mapel)
            ->where('jenis', $this->jenis)
            ->with(['siswa', 'pencatat']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('hapus_nilai_kelas')
                ->label('Hapus Nilai Kelas Ini')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Hapus Semua Nilai')
                ->modalDescription('Semua data nilai kelas ini akan dihapus permanen. Lanjutkan?')
                ->action(function () {
                    $deleted = Nilai::where('kelas_id', $this->kelas)
                        // ->whereId('id', $this->id)
                        ->delete();

                    Notification::make()
                        ->title("{$deleted} data nilai berhasil dihapus")
                        ->success()
                        ->send();

                    $this->redirect(NilaiResource::getUrl('index'));
                }),

            Action::make('kembali')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(NilaiResource::getUrl('index')),
        ];
    }
}