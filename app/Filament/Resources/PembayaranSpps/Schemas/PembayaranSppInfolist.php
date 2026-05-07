<?php

namespace App\Filament\Resources\PembayaranSpps\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PembayaranSppInfolist
{
    public static function configure(Schema $infolist): Schema
    {
        return $infolist->components([
            Section::make('Info Siswa')->columns(2)->schema([
                TextEntry::make('siswa.nama_lengkap')->label('Nama Siswa'),
                TextEntry::make('siswa.nis')->label('NIS'),
                TextEntry::make('siswa.kelas.nama_kelas')->label('Kelas')->default('-'),
                TextEntry::make('user.name')->label('Diajukan Oleh'),
            ]),

            Section::make('Detail Pembayaran')->columns(2)->schema([
                TextEntry::make('periode')
                    ->label('Periode')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::createFromFormat('Y-m', $state)
                        ->locale('id')
                        ->translatedFormat('F Y')),

                TextEntry::make('nominal')
                    ->label('Nominal')
                    ->money('IDR'),

                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'menunggu'     => 'warning',
                        'dikonfirmasi' => 'success',
                        'ditolak'      => 'danger',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'menunggu'     => 'Menunggu',
                        'dikonfirmasi' => 'Lunas',
                        'ditolak'      => 'Ditolak',
                        default        => '-',
                    }),

                TextEntry::make('catatan_ortu')->label('Catatan Ortu')->default('-'),
                TextEntry::make('catatan_admin')->label('Catatan Admin')->default('-'),
                TextEntry::make('dikonfirmasiOleh.name')->label('Dikonfirmasi Oleh')->default('-'),
                TextEntry::make('dikonfirmasi_at')->label('Dikonfirmasi Pada')->dateTime('d M Y H:i')->default('-'),
            ]),

            Section::make('Bukti Pembayaran')->schema([
                ImageEntry::make('bukti_pembayaran')
                    ->label('')
                    ->disk('public')
                    ->height(300),
            ]),
        ]);
    }
}