<?php

namespace App\Filament\Resources\Jurnals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JurnalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('guru.nama_lengkap')
                    ->label('Guru')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('materi')
                    ->label('Materi')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('pertemuan_ke')
                    ->label('Pertemuan')
                    ->prefix('Ke-')
                    ->sortable(),

                TextColumn::make('capaian')
                    ->label('Capaian')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'tercapai' => 'Tercapai',
                        'sebagian' => 'Sebagian',
                        'belum'    => 'Belum Tercapai',
                        default    => '-',
                    })
                    ->color(fn($state) => match($state) {
                        'tercapai' => 'success',
                        'sebagian' => 'warning',
                        'belum'    => 'danger',
                        default    => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'draft'     => 'Draft',
                        'submitted' => 'Terkirim',
                        default     => '-',
                    })
                    ->color(fn($state) => match($state) {
                        'draft'     => 'gray',
                        'submitted' => 'success',
                        default     => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->options(
                        \App\Models\Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')
                    ),

                SelectFilter::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(
                        \App\Models\MataPelajaran::orderBy('nama')->pluck('nama', 'id')
                    ),

                SelectFilter::make('capaian')
                    ->label('Capaian')
                    ->options([
                        'tercapai' => 'Tercapai',
                        'sebagian' => 'Sebagian Tercapai',
                        'belum'    => 'Belum Tercapai',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft'     => 'Draft',
                        'submitted' => 'Terkirim',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal', 'desc');
    }
}