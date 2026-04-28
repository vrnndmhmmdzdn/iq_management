<?php

namespace App\Filament\Resources\Nilais\Tables;

use App\Filament\Resources\Nilais\NilaiResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NilaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                
                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
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
                        'harian' => 'Harian',
                        'tugas'  => 'Tugas',
                        'uts'    => 'UTS',
                        'uas'    => 'UAS',
                        default  => '-',
                    }),
                TextColumn::make('rata_rata')
                    ->label('Rata-rata')
                    ->badge()
                    ->color(fn($state) => match(true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        default      => 'danger',
                    })
                    ->formatStateUsing(fn($state) => number_format($state, 1)),
                TextColumn::make('mataPelajaran.guruMapel.guru.name')
                    ->label('Guru Mapel')
                    ->default('-')
                    ->getStateUsing(fn($record) => $record->mataPelajaran?->guruMapel?->guru?->name ?? '-'),
                TextColumn::make('jumlah_siswa')
                    ->label('Jumlah Siswa')
                    ->suffix(' siswa'),
                TextColumn::make('tanggal_ujian')
                    ->label('Tanggal Ujian')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama_kelas'),
                SelectFilter::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->relationship('mataPelajaran', 'nama'),
                SelectFilter::make('jenis')
                    ->label('Jenis Penilaian')
                    ->options([
                        'harian' => 'Harian',
                        'tugas'  => 'Tugas',
                        'uts'    => 'UTS',
                        'uas'    => 'UAS',
                    ]),
            ])
            ->recordUrl(fn($record) => NilaiResource::getUrl('detail', [
                'kelas'  => $record->kelas_id,
                'mapel'  => $record->mata_pelajaran_id,
                'jenis'  => $record->jenis,
                'tanggal' => $record->tanggal_ujian,
            ]))
            ->recordActions([
                // Action::make('lihat_detail')
                //     ->label('Detail')
                //     ->icon('heroicon-o-eye')
                //     ->url(fn($record) => NilaiResource::getUrl('detail', [
                //         'kelas'  => $record->kelas_id,
                //         'mapel'  => $record->mata_pelajaran_id,
                //         'jenis'  => $record->jenis,
                //         'tanggal'  => $record->tanggal_ujian,
                //     ])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('kelas_id');
    }
}