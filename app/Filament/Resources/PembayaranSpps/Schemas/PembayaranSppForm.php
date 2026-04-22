<?php

namespace App\Filament\Resources\PembayaranSpps\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PembayaranSppForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('siswa_id')
                    ->required()
                    ->numeric(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('periode')
                    ->required(),
                TextInput::make('nominal')
                    ->required()
                    ->numeric(),
                TextInput::make('bukti_pembayaran')
                    ->required(),
                Textarea::make('catatan_ortu')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('catatan_admin')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['menunggu' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'ditolak' => 'Ditolak'])
                    ->default('menunggu')
                    ->required(),
                TextInput::make('dikonfirmasi_oleh')
                    ->numeric()
                    ->default(null),
                DateTimePicker::make('dikonfirmasi_at'),
            ]);
    }
}
