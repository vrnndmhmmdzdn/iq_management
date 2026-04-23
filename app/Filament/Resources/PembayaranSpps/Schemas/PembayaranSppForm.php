<?php

namespace App\Filament\Resources\PembayaranSpps\Schemas;

use Filament\Forms;
use Filament\Forms\Form;

class PembayaranSppForm
{
    public static function configure(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\Select::make('siswa_id')
                    ->label('Siswa')
                    ->relationship('siswa', 'nama_lengkap')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('periode')
                    ->label('Periode (YYYY-MM)')
                    ->required()
                    ->placeholder('2025-01'),

                Forms\Components\TextInput::make('nominal')
                    ->label('Nominal')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'menunggu'     => 'Menunggu',
                        'dikonfirmasi' => 'Dikonfirmasi',
                        'ditolak'      => 'Ditolak',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('catatan_admin')
                    ->label('Catatan Admin')
                    ->columnSpanFull(),
            ]),
        ]);
    }
}