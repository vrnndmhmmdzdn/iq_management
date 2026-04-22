<?php

namespace App\Filament\Resources\PembayaranSpps\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PembayaranSppInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('siswa_id')
                    ->numeric(),
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('periode'),
                TextEntry::make('nominal')
                    ->numeric(),
                TextEntry::make('bukti_pembayaran'),
                TextEntry::make('catatan_ortu')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('catatan_admin')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('dikonfirmasi_oleh')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('dikonfirmasi_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
