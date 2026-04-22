<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nis')
                    ->required(),
                TextInput::make('nisn')
                    ->default(null),
                TextInput::make('nama_lengkap')
                    ->required(),
                Select::make('jenis_kelamin')
                    ->options(['L' => 'L', 'P' => 'P'])
                    ->required(),
                TextInput::make('tempat_lahir')
                    ->default(null),
                DatePicker::make('tanggal_lahir'),
                Textarea::make('alamat')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('foto')
                    ->default(null),
                Select::make('status')
                    ->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'lulus' => 'Lulus'])
                    ->default('aktif')
                    ->required(),
                TextInput::make('kelas_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('tahun_ajaran_id')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
