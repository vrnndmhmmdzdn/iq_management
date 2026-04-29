<?php

namespace App\Filament\Resources\Jurnals\Pages;

use App\Filament\Resources\Jurnals\JurnalResource;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\JurnalGuru;
use App\Models\TahunAjaran;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DetailJurnalGuru extends ListRecords
{
    protected static string $resource = JurnalResource::class;

    public string $guruId  = '';
    public string $tanggal = '';

    public function mount(): void
    {
        $this->guruId  = request()->route('guru');
        $this->tanggal = request()->route('tanggal');
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
        $guru = Guru::find($this->guruId);
        $tgl  = $this->getTanggalObj()->locale('id')->translatedFormat('l, d F Y');
        return "Jurnal — {$guru?->nama_lengkap} — {$tgl}";
    }

    protected function getTableQuery(): Builder
    {
        $hari          = strtolower($this->getTanggalObj()->locale('id')->translatedFormat('l'));
        $tahunAjaranId = TahunAjaran::where('is_aktif', true)->first()?->id;
        $guruId        = $this->guruId;
        $tanggal       = $this->tanggal;

        // Base: semua jadwal guru ini di hari tersebut
        return JadwalPelajaran::query()
            ->where('guru_id', $guruId)
            ->where('hari', $hari)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->with(['kelas', 'mataPelajaran'])
            ->withExists([
                'jurnalHariIni as sudah_isi' => fn($q) => $q
                    ->whereDate('tanggal', $tanggal),
            ])
            ->orderBy('jam_mulai');
    }

    public function table(Table $table): Table
    {
        $tanggal = $this->tanggal;
        $guruId  = $this->guruId;

        return $table
            ->query($this->getTableQuery())
            ->contentGrid(['md' => 1, 'xl' => 1])
            ->columns([
                TextColumn::make('jadwal_card')
                    ->label('')
                    ->html()
                    ->getStateUsing(function (JadwalPelajaran $record) use ($tanggal) {
                        $kelas  = e($record->kelas?->nama_kelas ?? '-');
                        $mapel  = e($record->mataPelajaran?->nama ?? '-');
                        $mulai  = Carbon::parse($record->jam_mulai)->format('H:i');
                        $selesai = Carbon::parse($record->jam_selesai)->format('H:i');
                        $hari   = e($record->hari_label);

                        if ($record->sudah_isi) {
                            $jurnal = JurnalGuru::where('guru_id', $record->guru_id)
                                ->where('kelas_id', $record->kelas_id)
                                ->where('mata_pelajaran_id', $record->mata_pelajaran_id)
                                ->whereDate('tanggal', $tanggal)
                                ->first();

                            $materi    = e($jurnal?->materi ?? '-');
                            $pertemuan = $jurnal?->pertemuan_ke ?? '-';

                            [$capaianBg, $capaianText] = match($jurnal?->capaian) {
                                'tercapai' => ['#dcfce7', '#166534'],
                                'sebagian' => ['#fef9c3', '#854d0e'],
                                'belum'    => ['#fee2e2', '#991b1b'],
                                default    => ['#f3f4f6', '#374151'],
                            };
                            $capaianLabel = match($jurnal?->capaian) {
                                'tercapai' => 'Tercapai',
                                'sebagian' => 'Sebagian',
                                'belum'    => 'Belum Tercapai',
                                default    => '-',
                            };

                            [$statusBg, $statusText] = $jurnal?->status === 'submitted'
                                ? ['#dcfce7', '#166534']
                                : ['#f3f4f6', '#374151'];
                            $statusLabel = $jurnal?->status === 'submitted' ? 'Terkirim' : 'Draft';

                            $badge = 'display:inline-flex;align-items:center;padding:2px 10px;border-radius:9999px;font-size:12px;font-weight:500;';

                            return <<<HTML
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:4px 0;">
                                <div style="flex:1;min-width:0;">
                                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px;">
                                        <span style="{$badge}background:#dbeafe;color:#1e40af;">{$mapel}</span>
                                        <span style="{$badge}background:#ede9fe;color:#5b21b6;">{$kelas}</span>
                                        <span style="{$badge}background:{$capaianBg};color:{$capaianText};">{$capaianLabel}</span>
                                        <span style="{$badge}background:{$statusBg};color:{$statusText};">{$statusLabel}</span>
                                    </div>
                                    <p style="font-weight:600;font-size:15px;margin:0 0 4px;">{$materi}</p>
                                    <p style="font-size:13px;color:#6b7280;margin:0;">Pertemuan ke-{$pertemuan}</p>
                                </div>
                                <div style="text-align:right;flex-shrink:0;">
                                    <p style="font-size:11px;color:#9ca3af;margin:0 0 2px;">Jam</p>
                                    <p style="font-size:13px;font-weight:600;margin:0;">{$mulai} – {$selesai}</p>
                                </div>
                            </div>
                            HTML;
                        }

                        // Belum isi jurnal
                        return <<<HTML
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding:4px 0;">
                            <div style="flex:1;">
                                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px;">
                                    <span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:9999px;font-size:12px;font-weight:500;background:#dbeafe;color:#1e40af;">{$mapel}</span>
                                    <span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:9999px;font-size:12px;font-weight:500;background:#ede9fe;color:#5b21b6;">{$kelas}</span>
                                    <span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:9999px;font-size:12px;font-weight:500;background:#fee2e2;color:#991b1b;">⚠ Belum Isi Jurnal</span>
                                </div>
                                <p style="font-size:13px;color:#9ca3af;margin:0;font-style:italic;">Jurnal belum diisi untuk sesi ini</p>
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <p style="font-size:11px;color:#9ca3af;margin:0 0 2px;">Jam</p>
                                <p style="font-size:13px;font-weight:600;margin:0;">{$mulai} – {$selesai}</p>
                            </div>
                        </div>
                        HTML;
                    }),
            ])
            ->recordUrl(function (JadwalPelajaran $record) use ($tanggal) {
                if (! $record->sudah_isi) return null;

                $jurnal = JurnalGuru::where('guru_id', $record->guru_id)
                    ->where('kelas_id', $record->kelas_id)
                    ->where('mata_pelajaran_id', $record->mata_pelajaran_id)
                    ->whereDate('tanggal', $tanggal)
                    ->first();

                return $jurnal
                    ? JurnalResource::getUrl('view', ['record' => $jurnal])
                    : null;
            })
            ->paginated(false)
            ->emptyStateHeading('Tidak ada jadwal')
            ->emptyStateDescription('Guru ini tidak memiliki jadwal pada hari ini.')
            ->emptyStateIcon('heroicon-o-calendar');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('kemarin')
                ->label('‹ Kemarin')
                ->color('gray')
                ->url(JurnalResource::getUrl('detail', [
                    'guru'    => $this->guruId,
                    'tanggal' => Carbon::parse($this->tanggal)->subDay()->format('Y-m-d'),
                ])),

            Action::make('pilih_tanggal')
                ->label($this->getTanggalObj()->locale('id')->translatedFormat('d M Y'))
                ->icon('heroicon-o-calendar-days')
                ->color('gray')
                ->form([
                    DatePicker::make('tanggal')
                        ->label('Pilih Tanggal')
                        ->default($this->tanggal)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->redirect(JurnalResource::getUrl('detail', [
                        'guru'    => $this->guruId,
                        'tanggal' => $data['tanggal'],
                    ]));
                }),

            Action::make('besok')
                ->label('Besok ›')
                ->color('gray')
                ->visible(fn() => ! $this->isHariIni())
                ->url(JurnalResource::getUrl('detail', [
                    'guru'    => $this->guruId,
                    'tanggal' => Carbon::parse($this->tanggal)->addDay()->format('Y-m-d'),
                ])),

            Action::make('hari_ini')
                ->label('Hari Ini')
                ->color('info')
                ->visible(fn() => ! $this->isHariIni())
                ->url(JurnalResource::getUrl('detail', [
                    'guru'    => $this->guruId,
                    'tanggal' => now()->toDateString(),
                ])),

            Action::make('kembali')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(JurnalResource::getUrl('index')),
        ];
    }
}