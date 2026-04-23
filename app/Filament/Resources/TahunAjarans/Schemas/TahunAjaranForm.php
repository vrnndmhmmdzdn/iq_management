<?php

namespace App\Filament\Resources\TahunAjarans\Schemas;

use Filament\Forms;
use Filament\Forms\Form;

class TahunAjaranForm
{
    public static function configure(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Tahun Ajaran')
                    ->required()
                    ->placeholder('Contoh: 2024/2025')
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->displayFormat('d/m/Y'),

                Forms\Components\DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->displayFormat('d/m/Y'),

                Forms\Components\Toggle::make('is_aktif')
                    ->label('Jadikan Aktif')
                    ->helperText('Hanya satu tahun ajaran yang bisa aktif')
                    ->columnSpanFull(),
            ]),
        ]);
    }
}