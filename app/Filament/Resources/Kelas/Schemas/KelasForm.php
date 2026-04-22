<?php

namespace App\Filament\Resources\Kelas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KelasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_kelas')
                    ->required(),
                TextInput::make('tingkat')
                    ->required()
                    ->numeric(),
                TextInput::make('wali_kelas_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('tahun_ajaran_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
