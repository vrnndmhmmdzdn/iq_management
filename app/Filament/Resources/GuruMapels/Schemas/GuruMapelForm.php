<?php

namespace App\Filament\Resources\GuruMapels\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GuruMapelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                Select::make('guru_id')
                    ->label('Guru')
                    ->relationship('guru', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->relationship('mataPelajaran', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]),
        ]);
    }
}