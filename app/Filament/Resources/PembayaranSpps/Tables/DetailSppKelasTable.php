<?php

namespace App\Filament\Resources\PembayaranSpps\Tables;

use App\Filament\Resources\PembayaranSpps\PembayaranSppResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailSppKelasTable
{
    public static function configureIndex(Table $table, string $periode): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('wali_kelas_nama')
                    ->label('Wali Kelas')
                    ->default('-'),
                TextColumn::make('total_siswa')
                    ->label('Total Siswa')
                    ->suffix(' siswa'),
                TextColumn::make('sudah_bayar')
                    ->label('Lunas')
                    ->badge()
                    ->color('success'),
                TextColumn::make('menunggu')
                    ->label('Menunggu')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('belum_bayar')
                    ->label('Belum Bayar')
                    ->badge()
                    ->color('danger'),
            ])
            ->recordUrl(fn($record) => PembayaranSppResource::getUrl('detail-kelas', [
                'kelas'   => $record->id,
                'periode' => $periode,
            ]))
            ->recordActions([])
            ->toolbarActions([])
            ->defaultSort('nama_kelas');
    }
}