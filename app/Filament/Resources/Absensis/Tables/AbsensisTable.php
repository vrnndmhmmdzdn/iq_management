<?php

namespace App\Filament\Resources\Absensis\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AbsensisTable
{
    public static function configure(Table $table): Table
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
                TextColumn::make('jumlah_absen')
                    ->label('Total Siswa')
                    ->suffix(' siswa'),
                TextColumn::make('jumlah_hadir')
                    ->label('Hadir')
                    ->badge()
                    ->color('success'),
                TextColumn::make('jumlah_izin')
                    ->label('Izin')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('jumlah_sakit')
                    ->label('Sakit')
                    ->badge()
                    ->color('info'),
                TextColumn::make('jumlah_alfa')
                    ->label('Alfa')
                    ->badge()
                    ->color('danger'),
            ])
            ->recordUrl(fn($record) => \App\Filament\Resources\Absensis\AbsensiResource::getUrl('detail', [
                'kelas'   => $record->kelas_id,
                'tanggal' => is_string($record->tanggal)
                    ? $record->tanggal
                    : $record->tanggal->format('Y-m-d'),
            ]))
            ->recordActions([
                
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama_kelas');
    }
}