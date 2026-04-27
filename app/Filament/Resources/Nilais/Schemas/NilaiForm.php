<?php

namespace App\Filament\Resources\Nilais\Schemas;

use App\Models\Kelas;
use App\Models\Siswa;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NilaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                Select::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama_kelas')
                    ->searchable()
                    // ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($set) => $set('siswa_id', null)),

                Select::make('siswa_id')
                    ->label('Siswa')
                    ->required()
                    ->searchable()
                    ->options(function ($get) {
                        $kelasId = $get('kelas_id');
                        if (! $kelasId) return [];
                        return Siswa::where('kelas_id', $kelasId)
                            ->whereNull('deleted_at')
                            ->orderBy('nama_lengkap')
                            ->pluck('nama_lengkap', 'id');
                    }),

                Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->relationship('mataPelajaran', 'nama')
                    ->searchable()
                    // ->preload()
                    ->required(),

                Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship('tahunAjaran', 'nama')
                    ->searchable()
                    // ->preload()
                    ->required()
                    ->default(fn() => \App\Models\TahunAjaran::where('is_aktif', true)->first()?->id),

                Select::make('jenis')
                    ->label('Jenis Penilaian')
                    ->options([
                        'harian' => 'Harian',
                        'tugas'  => 'Tugas',
                        'uts'    => 'UTS',
                        'uas'    => 'UAS',
                    ])
                    ->required(),

                TextInput::make('nilai')
                    ->label('Nilai')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->required(),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->columnSpanFull()
                    ->nullable(),
            ]),
        ]);
    }
}