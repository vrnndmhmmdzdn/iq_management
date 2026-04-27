<?php

namespace App\Filament\Resources\Nilais\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailNilaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siswa.nama_lengkap')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nilai')
                    ->label('Nilai')
                    ->badge()
                    ->sortable()
                    ->color(fn($state) => match(true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        default      => 'danger',
                    }),
                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'harian' => 'info',
                        'tugas'  => 'warning',
                        'uts'    => 'primary',
                        'uas'    => 'success',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'harian' => 'Harian', 'tugas' => 'Tugas',
                        'uts'    => 'UTS',    'uas'   => 'UAS',
                        default  => '-',
                    }),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->default('-'),
                TextColumn::make('pencatat.name')
                    ->label('Dicatat Oleh')
                    ->default('-'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                // DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}