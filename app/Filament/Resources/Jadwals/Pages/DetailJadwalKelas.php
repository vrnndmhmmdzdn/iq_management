<?php

namespace App\Filament\Resources\Jadwals\Pages;

use App\Filament\Resources\Jadwals\JadwalResource;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DetailJadwalKelas extends ListRecords
{
    protected static string $resource = JadwalResource::class;

    public string $kelasId = '';
    public string $hari    = 'semua';

    public function mount(): void
    {
        $this->kelasId = request()->route('kelas');
        $this->hari    = request()->route('hari', 'semua');
    }

    public function getTitle(): string
    {
        $kelas = Kelas::find($this->kelasId);
        return 'Jadwal — ' . ($kelas?->nama_kelas ?? '-');
    }

    public function getTabs(): array
    {
        return [
            'semua'  => Tab::make('Semua'),
            'senin'  => Tab::make('Senin'),
            'selasa' => Tab::make('Selasa'),
            'rabu'   => Tab::make('Rabu'),
            'kamis'  => Tab::make('Kamis'),
            'jumat'  => Tab::make('Jumat'),
            'sabtu'  => Tab::make('Sabtu'),
        ];
    }

    public function getDefaultActiveTab(): string
    {
        return $this->hari;
    }

    protected function getTableQuery(): Builder
    {
        $tahunAjaranId = TahunAjaran::aktif()?->id;
        $activeTab     = $this->activeTab ?? $this->hari;

        return JadwalPelajaran::query()
            ->where('kelas_id', $this->kelasId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->when($activeTab !== 'semua', fn($q) => $q->where('hari', $activeTab))
            ->with(['guru', 'mataPelajaran'])
            ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu')")
            ->orderBy('jam_mulai');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
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
                    ->color('info'),

                TextColumn::make('jam_mulai')
                    ->label('Jam')
                    ->formatStateUsing(fn($state, $record) =>
                        \Carbon\Carbon::parse($state)->format('H:i') . ' – ' .
                        \Carbon\Carbon::parse($record->jam_selesai)->format('H:i')
                    ),

                TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guru.nama_lengkap')
                    ->label('Guru')
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false)
            ->emptyStateHeading('Tidak ada jadwal')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('kembali')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(JadwalResource::getUrl('index')),

            CreateAction::make()
                ->label('Tambah Jadwal')
                ->icon('heroicon-o-plus'),
        ];
    }
}