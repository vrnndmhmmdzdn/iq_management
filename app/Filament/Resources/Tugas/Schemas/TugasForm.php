<?php

namespace App\Filament\Resources\Tugas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TugasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('judul')
                    ->required(),
                Textarea::make('deskripsi')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('mata_pelajaran_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('kelas_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('created_by')
                    ->required()
                    ->numeric(),
                DatePicker::make('tanggal')
                    ->required(),
                TimePicker::make('batas_waktu'),
                TextInput::make('file_lampiran')
                    ->default(null),
                Toggle::make('is_aktif')
                    ->required(),
            ]);
    }
}
