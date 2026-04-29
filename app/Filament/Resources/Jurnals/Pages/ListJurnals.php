<?php

namespace App\Filament\Resources\Jurnals\Pages;

use App\Filament\Resources\Jurnals\JurnalResource;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\JurnalGuru;
use App\Models\TahunAjaran;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ListJurnals extends ListRecords
{
    protected static string $resource = JurnalResource::class;

    public string $tanggal = '';

    public function mount(): void
    {
        parent::mount();
        $this->tanggal = request('tanggal', now()->toDateString());
    }

    public function getTanggalObj(): Carbon
    {
        return Carbon::parse($this->tanggal);
    }

    public function isHariIni(): bool
    {
        return $this->getTanggalObj()->isToday();
    }

    public function getTitle(): string
    {
        return 'Jurnal Guru';
    }

    protected function getTableQuery(): Builder
    {
        $hariIni       = strtolower($this->getTanggalObj()->locale('id')->translatedFormat('l'));
        $tahunAjaranId = TahunAjaran::where('is_aktif', true)->first()?->id;
        $tanggal       = $this->tanggal;

        return Guru::query()
            ->where('status', 'aktif')
            ->with(['user'])
            ->whereHas('jadwals', fn($q) => $q
                ->where('hari', $hariIni)
                ->where('tahun_ajaran_id', $tahunAjaranId)
            )
            ->withCount([
                'jadwals as total_jadwal' => fn($q) => $q
                    ->where('hari', $hariIni)
                    ->where('tahun_ajaran_id', $tahunAjaranId),
                'jurnals as total_jurnal' => fn($q) => $q
                    ->whereDate('tanggal', $tanggal),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('nama_lengkap')
                    ->label('Nama Guru')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_jadwal')
                    ->label('Total Sesi')
                    ->suffix(' sesi')
                    ->sortable(),

                TextColumn::make('total_jurnal')
                    ->label('Sudah Diisi')
                    ->suffix(fn($record) => ' / ' . $record->total_jadwal . ' sesi')
                    ->badge()
                    ->color(fn($record) => match(true) {
                        $record->total_jurnal === 0                               => 'danger',
                        $record->total_jurnal < $record->total_jadwal             => 'warning',
                        default                                                   => 'success',
                    }),

                TextColumn::make('status_jurnal')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn($record) => match(true) {
                        $record->total_jurnal === 0                               => 'Belum Isi',
                        $record->total_jurnal < $record->total_jadwal             => 'Sebagian',
                        default                                                   => 'Lengkap',
                    })
                    ->color(fn($record) => match(true) {
                        $record->total_jurnal === 0                               => 'danger',
                        $record->total_jurnal < $record->total_jadwal             => 'warning',
                        default                                                   => 'success',
                    }),
            ])
            ->recordUrl(fn($record) => JurnalResource::getUrl('detail', [
                'guru'    => $record->id,
                'tanggal' => $this->tanggal,
            ]))
            ->defaultSort('nama_lengkap')
            ->paginated(false)
            ->emptyStateHeading('Tidak ada jadwal hari ini')
            ->emptyStateIcon('heroicon-o-book-open');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('kemarin')
                ->label('‹ Kemarin')
                ->color('gray')
                ->action(function () {
                    $this->tanggal = Carbon::parse($this->tanggal)->subDay()->format('Y-m-d');
                    $this->resetTable();
                }),

            Action::make('pilih_tanggal')
                ->label(Carbon::parse($this->tanggal)->locale('id')->translatedFormat('d M Y'))
                ->icon('heroicon-o-calendar-days')
                ->color('gray')
                ->form([
                    DatePicker::make('tanggal')
                        ->label('Pilih Tanggal')
                        ->default($this->tanggal)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->tanggal = $data['tanggal'];
                    $this->resetTable();
                }),

            Action::make('besok')
                ->label('Besok ›')
                ->color('gray')
                ->visible(fn() => ! $this->isHariIni())
                ->action(function () {
                    $this->tanggal = Carbon::parse($this->tanggal)->addDay()->format('Y-m-d');
                    $this->resetTable();
                }),

            Action::make('hari_ini')
                ->label('Hari Ini')
                ->color('info')
                ->visible(fn() => ! $this->isHariIni())
                ->action(function () {
                    $this->tanggal = now()->toDateString();
                    $this->resetTable();
                }),

            CreateAction::make()
                ->label('Buat Jurnal')
                ->icon('heroicon-o-pencil-square'),
                // ->url(fn() => JurnalResource::getUrl('create', [
                //     'tanggal' => $this->tanggal,
                // ])),
        ];
    }
}