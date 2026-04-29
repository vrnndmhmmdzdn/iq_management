<?php

namespace App\Filament\Resources\Ekskuls\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EkskulForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_ekskul')
                    ->label('Ekstrakurikuler')
                    ->required(),
                TextInput::make('deskripsi')
                    ->label('Deskripsi')
                    ->nullable(),
                Select::make('pembina_id')
                    ->label('Pembina')
                    ->relationship('pembina', 'nama')
                    ->searchable()
                    ->nullable(),
                Toggle::make('is_aktif')
                    ->label('Aktif')
                    ->default(true)
            ])
            ->columns(2)
            ;

    }
}
