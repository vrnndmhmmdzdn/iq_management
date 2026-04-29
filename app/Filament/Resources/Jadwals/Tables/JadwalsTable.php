<?php

namespace App\Filament\Resources\Jadwals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JadwalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hari')
                    ->label('Hari')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'senin'  => 'Senin',  'selasa' => 'Selasa',
                        'rabu'   => 'Rabu',   'kamis'  => 'Kamis',
                        'jumat'  => 'Jumat',  'sabtu'  => 'Sabtu',
                        default  => '-',
                    })
                    ->color('info')
                    ->sortable(),

                TextColumn::make('jam_mulai')
                    ->label('Jam')
                    ->formatStateUsing(fn($state, $record) =>
                        \Carbon\Carbon::parse($state)->format('H:i') . ' – ' .
                        \Carbon\Carbon::parse($record->jam_selesai)->format('H:i')
                    ),

                TextColumn::make('guru.nama_lengkap')
                    ->label('Guru')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tahunAjaran.nama')
                    ->label('Tahun Ajaran')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('hari')
                    ->label('Hari')
                    ->options([
                        'senin'  => 'Senin',  'selasa' => 'Selasa',
                        'rabu'   => 'Rabu',   'kamis'  => 'Kamis',
                        'jumat'  => 'Jumat',  'sabtu'  => 'Sabtu',
                    ]),

                SelectFilter::make('guru_id')
                    ->label('Guru')
                    ->options(
                        \App\Models\Guru::where('status', 'aktif')
                            ->orderBy('nama_lengkap')
                            ->pluck('nama_lengkap', 'id')
                    ),

                SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->options(
                        \App\Models\Kelas::orderBy('nama_kelas')
                            ->pluck('nama_kelas', 'id')
                    ),

                SelectFilter::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(
                        \App\Models\MataPelajaran::orderBy('nama')
                            ->pluck('nama', 'id')
                    ),

                SelectFilter::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(
                        \App\Models\TahunAjaran::orderByDesc('is_aktif')
                            ->pluck('nama', 'id')
                    ),
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
            ->defaultSort('hari');
    }
}