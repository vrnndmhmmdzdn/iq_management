<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Infolists;
use Filament\Infolists\Infolist;

class SiswaInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Data Pribadi')
                ->columns(2)
                ->schema([
                    Infolists\Components\ImageEntry::make('foto')
                        ->label('')
                        ->circular()
                        ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->nama_lengkap).'&color=3b82f6&background=dbeafe')
                        ->columnSpanFull(),

                    Infolists\Components\TextEntry::make('nama_lengkap')->label('Nama Lengkap'),
                    Infolists\Components\TextEntry::make('nis')->label('NIS'),
                    Infolists\Components\TextEntry::make('nisn')->label('NISN')->default('-'),
                    Infolists\Components\TextEntry::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->formatStateUsing(fn($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                    Infolists\Components\TextEntry::make('tempat_lahir')->label('Tempat Lahir')->default('-'),
                    Infolists\Components\TextEntry::make('tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->date('d M Y')
                        ->default('-'),
                    Infolists\Components\TextEntry::make('alamat')->label('Alamat')->default('-')->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Data Sekolah')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('kelas.nama_kelas')->label('Kelas')->default('-'),
                    Infolists\Components\TextEntry::make('tahunAjaran.nama')->label('Tahun Ajaran')->default('-'),
                    Infolists\Components\TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn($state) => match($state) {
                            'aktif'    => 'success',
                            'nonaktif' => 'gray',
                            'lulus'    => 'info',
                            default    => 'gray',
                        }),
                    Infolists\Components\IconEntry::make('spp_lunas')
                        ->label('SPP Bulan Ini')
                        ->getStateUsing(fn($record) => $record->isSppLunas())
                        ->boolean(),
                ]),
        ]);
    }
}