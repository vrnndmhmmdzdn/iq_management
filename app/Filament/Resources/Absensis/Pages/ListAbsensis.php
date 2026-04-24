<?php

namespace App\Filament\Resources\Absensis\Pages;

use App\Filament\Resources\Absensis\AbsensiResource;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAbsensis extends ListRecords
{
    protected static string $resource = AbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('input_massal')
                ->label('Input Absensi Kelas')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('primary')
                ->form([
                    Select::make('kelas_id')
                        ->label('Kelas')
                        ->options(function () {
                            $user = auth()->user();
                            $query = Kelas::query();
                            if ($user->hasRole('guru')) {
                                $query->where('wali_kelas_id', $user->id);
                            }
                            return $query->orderBy('nama_kelas')->pluck('nama_kelas', 'id');
                        })
                        ->required(),
                    DatePicker::make('tanggal')
                        ->label('Tanggal')
                        ->default(today())
                        ->required(),
                    Select::make('default_status')
                        ->label('Status Default Semua Siswa')
                        ->options([
                            'hadir' => 'Hadir',
                            'izin'  => 'Izin',
                            'sakit' => 'Sakit',
                            'alfa'  => 'Alfa',
                        ])
                        ->default('hadir')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $siswas = Siswa::where('kelas_id', $data['kelas_id'])
                        ->whereNull('deleted_at')
                        ->get();

                    if ($siswas->isEmpty()) {
                        Notification::make()
                            ->title('Tidak ada siswa di kelas ini')
                            ->warning()->send();
                        return;
                    }

                    $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();

                    foreach ($siswas as $siswa) {
                        Absensi::updateOrCreate(
                            ['siswa_id' => $siswa->id, 'tanggal' => $data['tanggal']],
                            [
                                'kelas_id'        => $data['kelas_id'],
                                'tahun_ajaran_id' => $tahunAjaran?->id,
                                'status'          => $data['default_status'],
                                'dicatat_oleh'    => auth()->id(),
                            ]
                        );
                    }

                    Notification::make()
                        ->title("Absensi {$siswas->count()} siswa berhasil disimpan!")
                        ->success()->send();
                }),

            CreateAction::make()->label('Tambah Manual'),
        ];
    }
}