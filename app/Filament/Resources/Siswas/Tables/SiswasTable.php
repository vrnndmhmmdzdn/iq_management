<?php

namespace App\Filament\Resources\Siswas\Tables;

use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class SiswasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->nama_lengkap).'&color=3b82f6&background=dbeafe'),

                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => 'NIS: '.$record->nis),

                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('JK')
                    ->formatStateUsing(fn($state) => $state === 'L' ? 'L' : 'P')
                    ->badge()
                    ->color(fn($state) => $state === 'L' ? 'info' : 'danger'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'aktif'    => 'success',
                        'nonaktif' => 'gray',
                        'lulus'    => 'info',
                        default    => 'gray',
                    }),

                Tables\Columns\IconColumn::make('spp_lunas')
                    ->label('SPP')
                    ->getStateUsing(fn($record) => $record->isSppLunas())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama_kelas'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif'    => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        'lulus'    => 'Lulus',
                    ]),

                Tables\Filters\SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
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
            ->defaultSort('nama_lengkap');
    }
}