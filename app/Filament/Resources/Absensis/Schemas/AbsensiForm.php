<?php

namespace App\Filament\Resources\Absensis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AbsensiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('siswa_id')
                ->label('Siswa')
                ->relationship('siswa', 'nama_lengkap')
                ->searchable()
                // ->preload()
                ->required(),
            Select::make('kelas_id')
                ->label('Kelas')
                ->relationship('kelas', 'nama_kelas')
                ->required(),
            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->default(today())
                ->required(),
            Select::make('status')
                ->label('Status')
                ->options([
                    'hadir' => 'Hadir',
                    'izin'  => 'Izin',
                    'sakit' => 'Sakit',
                    'alfa'  => 'Alfa',
                ])
                ->default('hadir')
                ->required(),
            Textarea::make('keterangan')
                ->label('Keterangan')
                ->nullable(),
        ]);
    }
}