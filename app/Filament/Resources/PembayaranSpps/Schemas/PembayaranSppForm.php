<?php

namespace App\Filament\Resources\PembayaranSpps\Schemas;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;

class PembayaranSppForm
{
    public static function configure(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('siswa_id')
                    ->label('Siswa')
                    ->relationship('siswa', 'nama_lengkap')
                    ->searchable()
                    // ->preload()
                    ->required(),

                TextInput::make('periode')
                    ->label('Periode (YYYY-MM)')
                    ->required()
                    ->placeholder('2025-01'),

                TextInput::make('nominal')
                    ->label('Nominal')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'menunggu'     => 'Menunggu',
                        'dikonfirmasi' => 'Dikonfirmasi',
                        'ditolak'      => 'Ditolak',
                    ])
                    ->required(),

                Textarea::make('catatan_admin')
                    ->label('Catatan Admin')
                    ->columnSpanFull(),
            ]),
        ]);
    }
}