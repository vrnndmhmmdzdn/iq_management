<?php

namespace App\Filament\Resources\PembayaranSpps\Schemas;

use Filament\Infolists;
use Filament\Infolists\Infolist;

class PembayaranSppInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Info Siswa')->columns(2)->schema([
                Infolists\Components\TextEntry::make('siswa.nama_lengkap')->label('Nama Siswa'),
                Infolists\Components\TextEntry::make('siswa.nis')->label('NIS'),
                Infolists\Components\TextEntry::make('siswa.kelas.nama_kelas')->label('Kelas')->default('-'),
                Infolists\Components\TextEntry::make('user.name')->label('Diajukan Oleh'),
            ]),

            Infolists\Components\Section::make('Detail Pembayaran')->columns(2)->schema([
                Infolists\Components\TextEntry::make('periode')
                    ->label('Periode')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::createFromFormat('Y-m', $state)->locale('id')->translatedFormat('F Y')),

                Infolists\Components\TextEntry::make('nominal')
                    ->label('Nominal')
                    ->money('IDR'),

                Infolists\Components\TextEntry::make('status')
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

                Infolists\Components\TextEntry::make('catatan_ortu')->label('Catatan Ortu')->default('-'),
                Infolists\Components\TextEntry::make('catatan_admin')->label('Catatan Admin')->default('-'),
                Infolists\Components\TextEntry::make('dikonfirmasiOleh.name')->label('Dikonfirmasi Oleh')->default('-'),
                Infolists\Components\TextEntry::make('dikonfirmasi_at')->label('Dikonfirmasi Pada')->dateTime('d M Y H:i')->default('-'),
            ]),

            Infolists\Components\Section::make('Bukti Pembayaran')->schema([
                Infolists\Components\ImageEntry::make('bukti_pembayaran')
                    ->label('')
                    ->disk('public')
                    ->height(300),
            ]),
        ]);
    }
}