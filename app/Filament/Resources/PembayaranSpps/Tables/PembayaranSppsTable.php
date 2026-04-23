<?php

namespace App\Filament\Resources\PembayaranSpps\Tables;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;

class PembayaranSppsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siswa.nama_lengkap')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->siswa->kelas->nama_kelas ?? '-'),

                TextColumn::make('periode')
                    ->label('Periode')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::createFromFormat('Y-m', $state)->locale('id')->translatedFormat('F Y'))
                    ->sortable(),

                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
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

                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'menunggu'     => 'Menunggu',
                        'dikonfirmasi' => 'Dikonfirmasi',
                        'ditolak'      => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'menunggu')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalDescription('Tandai pembayaran ini sebagai LUNAS?')
                    ->form([
                        Textarea::make('catatan_admin')
                            ->label('Catatan (opsional)')
                            ->placeholder('Misal: Pembayaran diterima via BCA'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status'            => 'dikonfirmasi',
                            'catatan_admin'     => $data['catatan_admin'] ?? null,
                            'dikonfirmasi_oleh' => auth()->id(),
                            'dikonfirmasi_at'   => now(),
                        ]);
                    }),

                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'menunggu')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pembayaran')
                    ->form([
                        Forms\Components\Textarea::make('catatan_admin')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Misal: Bukti pembayaran tidak jelas'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status'            => 'ditolak',
                            'catatan_admin'     => $data['catatan_admin'],
                            'dikonfirmasi_oleh' => auth()->id(),
                            'dikonfirmasi_at'   => now(),
                        ]);
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}