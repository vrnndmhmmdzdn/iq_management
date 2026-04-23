<?php

namespace App\Filament\Resources\Kelas\Tables;

use Filament\Tables;
use Filament\Tables\Table;  
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;

class KelasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tingkat')
                    ->label('Tingkat')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn($state) => "Kelas $state")
                    ->sortable(),

                TextColumn::make('nama_kelas')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tahunAjaran.nama')
                    ->label('Tahun Ajaran')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('waliKelas.name')
                    ->label('Wali Kelas')
                    ->default('Belum ditentukan')
                    ->icon('heroicon-m-user'),

                TextColumn::make('siswa_count')
                    ->label('Jumlah Siswa')
                    ->counts('siswa')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                SelectFilter::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship('tahunAjaran', 'nama'),

                SelectFilter::make('tingkat')
                    ->label('Tingkat')
                    ->options(collect(range(1, 6))->mapWithKeys(fn($i) => [$i => "Kelas $i"])),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tingkat');
    }
}