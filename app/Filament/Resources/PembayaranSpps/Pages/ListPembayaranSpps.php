<?php

namespace App\Filament\Resources\PembayaranSpps\Pages;

use App\Filament\Resources\PembayaranSpps\PembayaranSppResource;
use App\Models\PembayaranSpp;
use App\Models\Siswa;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPembayaranSpps extends ListRecords
{
    protected static string $resource = PembayaranSppResource::class;

    public string $periodeFilter = '';

    public function mount(): void
    {
        parent::mount();
        $this->periodeFilter = now()->format('Y-m');
    }

    protected function getTableQuery(): Builder
    {
        return PembayaranSpp::query()
            ->where('periode', $this->periodeFilter)
            ->with(['siswa.kelas']);
    }

    public function getTabs(): array
    {
        $periode = $this->periodeFilter;

        return [
            'semua' => Tab::make('Semua')
                ->modifyQueryUsing(fn(Builder $query) => $query),

            'belum_bayar' => Tab::make('Belum Bayar')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'belum_bayar'))
                ->badge(PembayaranSpp::where('periode', $periode)->where('status', 'belum_bayar')->count())
                ->badgeColor('danger'),

            'menunggu' => Tab::make('Menunggu Konfirmasi')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'menunggu'))
                ->badge(PembayaranSpp::where('periode', $periode)->where('status', 'menunggu')->count())
                ->badgeColor('warning'),

            'dikonfirmasi' => Tab::make('Lunas')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'dikonfirmasi'))
                ->badge(PembayaranSpp::where('periode', $periode)->where('status', 'dikonfirmasi')->count())
                ->badgeColor('success'),

            'ditolak' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'ditolak'))
                ->badgeColor('gray'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pilih_periode')
                ->label(fn() => 'Periode: ' . Carbon::createFromFormat('Y-m', $this->periodeFilter)
                    ->locale('id')->translatedFormat('F Y'))
                ->icon('heroicon-o-calendar-days')
                ->color('gray')
                ->form([
                    Select::make('periode')
                        ->label('Pilih Periode')
                        ->options(function () {
                            $options = [];
                            for ($i = -3; $i <= 6; $i++) {
                                $date = now()->addMonths($i);
                                $key  = $date->format('Y-m');
                                $options[$key] = $date->locale('id')->translatedFormat('F Y');
                            }
                            return $options;
                        })
                        ->default($this->periodeFilter)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->periodeFilter = $data['periode'];
                }),

            Action::make('generate_tagihan')
                ->label('Generate Tagihan')
                ->icon('heroicon-o-bolt')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Generate Tagihan SPP')
                ->modalDescription('Tagihan akan dibuat untuk semua siswa aktif yang belum memiliki tagihan di periode ini.')
                ->form([
                    Select::make('periode')
                        ->label('Pilih Bulan Tagihan')
                        ->options(function () {
                            $options = [];
                            for ($i = -3; $i <= 12; $i++) {
                                $date = now()->addMonths($i);
                                $key  = $date->format('Y-m');
                                $options[$key] = $date->locale('id')->translatedFormat('F Y');
                            }
                            return $options;
                        })
                        ->default(now()->format('Y-m'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    $periode = $data['periode'];
                    $siswas  = Siswa::where('status', 'aktif')->whereNull('deleted_at')->get();
                    $adminId = auth()->id();
                    $count   = 0;

                    foreach ($siswas as $siswa) {
                        $sudahAda = PembayaranSpp::where('siswa_id', $siswa->id)
                            ->where('periode', $periode)
                            ->exists();

                        if ($sudahAda) continue;

                        PembayaranSpp::create([
                            'siswa_id'         => $siswa->id,
                            'user_id'          => $adminId,
                            'periode'          => $periode,
                            'nominal'          => $siswa->nominal_spp,
                            'bukti_pembayaran' => null,
                            'status'           => 'belum_bayar',
                            'is_tagihan'       => true,
                        ]);

                        $count++;
                    }

                    Notification::make()
                        ->title("{$count} tagihan berhasil digenerate!")
                        ->success()->send();

                    $this->periodeFilter = $periode;
                }),
        ];
    }
}