<?php

namespace App\Filament\Resources\Jadwals\Tables;

use App\Filament\Resources\Jadwals\JadwalResource;
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
                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guru.nama_lengkap')
                    ->label('Guru')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jam_mulai')
                    ->label('Jam')
                    ->formatStateUsing(fn($state, $record) =>
                        \Carbon\Carbon::parse($state)->format('H:i') . ' – ' .
                        \Carbon\Carbon::parse($record->jam_selesai)->format('H:i')
                    ),

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
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tahunAjaran.nama')
                    ->label('Tahun Ajaran')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->options(fn() =>
                        \App\Models\Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')
                    ),

                SelectFilter::make('guru_id')
                    ->label('Guru')
                    ->options(fn() =>
                        \App\Models\Guru::where('status', 'aktif')
                            ->orderBy('nama_lengkap')
                            ->pluck('nama_lengkap', 'id')
                    ),

                SelectFilter::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(fn() =>
                        \App\Models\MataPelajaran::orderBy('nama')->pluck('nama', 'id')
                    ),

                SelectFilter::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(fn() =>
                        \App\Models\TahunAjaran::orderByDesc('is_aktif')->pluck('nama', 'id')
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
            ]);
            // ->defaultSort('jadwal_pelajarans.jam_mulai');
    }
}