<?php

namespace App\Filament\Resources\Kelas\Schemas;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;

class KelasForm
{
    public static function configure(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('nama_kelas')
                    ->label('Nama Kelas')
                    ->required()
                    ->placeholder('Contoh: Kelas 1A')
                    ->maxLength(50),

                Forms\Components\Select::make('tingkat')
                    ->label('Tingkat')
                    ->required()
                    ->options(collect(range(1, 6))->mapWithKeys(fn($i) => [$i => "Kelas $i"])),

                Forms\Components\Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship('tahunAjaran', 'nama')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('wali_kelas_id')
                    ->label('Wali Kelas')
                    ->options(User::role('guru')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->placeholder('Pilih wali kelas'),
            ]),
        ]);
    }
}