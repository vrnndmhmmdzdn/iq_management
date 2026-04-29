<?php

namespace App\Filament\Resources\Jadwals\Schemas;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JadwalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(
                        TahunAjaran::orderByDesc('is_aktif')
                            ->get()
                            ->mapWithKeys(fn($ta) => [$ta->id => $ta->nama . ($ta->is_aktif ? ' (Aktif)' : '')])
                    )
                    ->default(fn() => TahunAjaran::aktif()?->id)
                    ->required(),

                Select::make('guru_id')
                    ->label('Guru')
                    ->options(
                        Guru::orderBy('nama_lengkap')
                            ->get()
                            ->mapWithKeys(fn($g) => [$g->id => $g->nama_lengkap])
                    )
                    ->searchable()
                    ->required(),

                Select::make('kelas_id')
                    ->label('Kelas')
                    ->options(
                        Kelas::orderBy('nama_kelas')
                            ->get()
                            ->mapWithKeys(fn($k) => [$k->id => $k->nama_kelas])
                    )
                    ->searchable()
                    ->required(),

                Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(
                        MataPelajaran::orderBy('nama')
                            ->get()
                            ->mapWithKeys(fn($m) => [$m->id => "[{$m->kode}] {$m->nama}"])
                    )
                    ->searchable()
                    ->required(),

                Select::make('hari')
                    ->label('Hari')
                    ->options([
                        'senin'  => 'Senin',
                        'selasa' => 'Selasa',
                        'rabu'   => 'Rabu',
                        'kamis'  => 'Kamis',
                        'jumat'  => 'Jumat',
                        'sabtu'  => 'Sabtu',
                    ])
                    ->required(),

                Select::make('dummy')
                    ->label('')
                    ->hidden(), // spacer biar layout rapi

                TimePicker::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->seconds(false)
                    ->required(),

                TimePicker::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->seconds(false)
                    ->required(),
            ]),
        ]);
    }
}