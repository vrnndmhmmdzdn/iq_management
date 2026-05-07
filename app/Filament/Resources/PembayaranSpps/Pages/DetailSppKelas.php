<?php

namespace App\Filament\Resources\PembayaranSpps\Pages;

use App\Filament\Resources\PembayaranSpps\PembayaranSppResource;
use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\Siswa;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DetailSppKelas extends ListRecords
{
    protected static string $resource = PembayaranSppResource::class;

    public string $kelas   = '';
    public string $periode = '';

    public function mount(): void
    {
        $this->kelas   = request()->route('kelas');
        $this->periode = request()->route('periode');
    }

    public function getTitle(): string
    {
        $kelas    = Kelas::find($this->kelas);
        $periodeLabel = Carbon::createFromFormat('Y-m', $this->periode)
            ->locale('id')->translatedFormat('F Y');
        return "SPP {$kelas?->nama_kelas} — {$periodeLabel}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => $this->getTableQuery())
            ->columns([
                TextColumn::make('siswa.nama_lengkap')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'belum_bayar'  => 'danger',
                        'menunggu'     => 'warning',
                        'dikonfirmasi' => 'success',
                        'ditolak'      => 'gray',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'belum_bayar'  => 'Belum Bayar',
                        'menunggu'     => 'Menunggu Konfirmasi',
                        'dikonfirmasi' => 'Lunas',
                        'ditolak'      => 'Ditolak',
                        default        => '-',
                    }),
                TextColumn::make('created_at')
                    ->label('Tanggal Bayar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->recordUrl(fn($record) => $record->status !== 'belum_bayar'
                ? PembayaranSppResource::getUrl('view', ['record' => $record])
                : null
            )
            ->recordActions([
                \Filament\Actions\Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'menunggu')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->form([
                        Textarea::make('catatan_admin')->label('Catatan (opsional)'),
                    ])
                    ->action(fn($record, array $data) => $record->update([
                        'status'            => 'dikonfirmasi',
                        'catatan_admin'     => $data['catatan_admin'] ?? null,
                        'dikonfirmasi_oleh' => auth()->id(),
                        'dikonfirmasi_at'   => now(),
                    ])),

                \Filament\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'menunggu')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('catatan_admin')->label('Alasan')->required(),
                    ])
                    ->action(fn($record, array $data) => $record->update([
                        'status'            => 'ditolak',
                        'catatan_admin'     => $data['catatan_admin'],
                        'dikonfirmasi_oleh' => auth()->id(),
                        'dikonfirmasi_at'   => now(),
                    ])),
            ])
            ->defaultSort('siswa.nama_lengkap');
    }

    protected function getTableQuery(): Builder
    {
        return PembayaranSpp::query()
            ->where('periode', $this->periode)
            ->whereHas('siswa', fn($q) => $q->where('kelas_id', $this->kelas))
            ->with(['siswa']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('kembali')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(PembayaranSppResource::getUrl('index')),
        ];
    }
}