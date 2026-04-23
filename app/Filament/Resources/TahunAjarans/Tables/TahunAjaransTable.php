<?php

namespace App\Filament\Resources\TahunAjarans\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
// use Filament\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class TahunAjaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date('d M Y'),

                TextColumn::make('tanggal_selesai')
                    ->label('Selesai')
                    ->date('d M Y'),

                IconColumn::make('is_aktif')
                    ->label('Aktif')
                    ->boolean(),
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
            ]);
    }
}