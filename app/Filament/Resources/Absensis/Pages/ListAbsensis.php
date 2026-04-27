<?php

namespace App\Filament\Resources\Absensis\Pages;

use App\Filament\Resources\Absensis\AbsensiResource;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Builder;

class ListAbsensis extends ListRecords
{
    protected static string $resource = AbsensiResource::class;

    public string $tanggalFilter;

    public function mount(): void
    {
        parent::mount();
        $this->tanggalFilter = today()->format('Y-m-d');
    }

    protected function getTableQuery(): Builder
    {
        return Absensi::query()
            ->selectRaw('
                MIN(absensis.id) as id,
                absensis.kelas_id,
                absensis.tanggal,
                COUNT(*) as jumlah_absen,
                SUM(CASE WHEN absensis.status = "hadir" THEN 1 ELSE 0 END) as jumlah_hadir,
                SUM(CASE WHEN absensis.status = "izin" THEN 1 ELSE 0 END) as jumlah_izin,
                SUM(CASE WHEN absensis.status = "sakit" THEN 1 ELSE 0 END) as jumlah_sakit,
                SUM(CASE WHEN absensis.status = "alfa" THEN 1 ELSE 0 END) as jumlah_alfa,
                kelas.nama_kelas,
                kelas.wali_kelas_id,
                users.name as wali_kelas_nama
            ')
            ->join('kelas', 'absensis.kelas_id', '=', 'kelas.id')
            ->leftJoin('users', 'users.id', '=', 'kelas.wali_kelas_id')
            ->whereDate('absensis.tanggal', $this->tanggalFilter)
            ->groupBy(
                'absensis.kelas_id',
                'absensis.tanggal',
                'kelas.nama_kelas',
                'kelas.wali_kelas_id',
                'users.name'
            );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pilih_tanggal')
                ->label(fn() => 'Tanggal: ' . Carbon::parse($this->tanggalFilter)->translatedFormat('d M Y'))
                ->icon('heroicon-o-calendar-days')
                ->color('gray')
                ->form([
                    DatePicker::make('tanggal')
                        ->label('Pilih Tanggal')
                        ->default($this->tanggalFilter)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->tanggalFilter = $data['tanggal'];
                }),

            Action::make('input_absensi')
                ->label('Input Absensi Kelas')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('primary')
                ->modalWidth('4xl')
                ->form([
                    Wizard::make([
                        Step::make('Pilih Kelas & Tanggal')
                            ->schema([
                                DatePicker::make('tanggal')
                                    ->label('Tanggal Absensi')
                                    ->default($this->tanggalFilter)
                                    ->required(),
                                Select::make('kelas_id')
                                    ->label('Pilih Kelas')
                                    ->options(function () {
                                        $user = auth()->user();
                                        $query = Kelas::query();
                                        if ($user->hasRole('guru')) {
                                            $query->where('wali_kelas_id', $user->id);
                                        }
                                        return $query->orderBy('nama_kelas')->pluck('nama_kelas', 'id');
                                    })
                                    ->required(),
                            ])
                            ->afterValidation(function (Set $set, $get) {
                                $kelasId = $get('kelas_id');
                                $tanggal = $get('tanggal');

                                if (! $kelasId || ! $tanggal) return;

                                // Cek apakah sudah ada absensi
                                $sudahAda = Absensi::where('kelas_id', $kelasId)
                                    ->whereDate('tanggal', $tanggal)
                                    ->exists();

                                if ($sudahAda) {
                                    Notification::make()
                                        ->title('Absensi sudah ada!')
                                        ->body('Absensi kelas ini untuk tanggal tersebut sudah pernah diinput. Klik row kelas untuk melihat atau mengedit.')
                                        ->warning()
                                        ->send();

                                    // Hentikan wizard, jangan lanjut ke step 2
                                    throw new Halt();
                                }

                                $existing = Absensi::where('kelas_id', $kelasId)
                                    ->whereDate('tanggal', $tanggal)
                                    ->get()
                                    ->keyBy('siswa_id');

                                $siswas = Siswa::where('kelas_id', $kelasId)
                                    ->whereNull('deleted_at')
                                    ->orderBy('nama_lengkap')
                                    ->get();

                                $set('siswa_list', $siswas->map(fn($s) => [
                                    'siswa_id'   => $s->id,
                                    'nama'       => $s->nama_lengkap,
                                    'status'     => $existing->get($s->id)?->status ?? 'hadir',
                                    'keterangan' => $existing->get($s->id)?->keterangan ?? '',
                                ])->toArray());
                            }),

                        Step::make('Input Absensi')
                            ->schema([
                                Repeater::make('siswa_list')
                                    ->label('Daftar Siswa')
                                    ->schema([
                                        Hidden::make('siswa_id'),
                                        TextInput::make('nama')
                                            ->label('Nama Siswa')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->columnSpan(2),
                                        Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'hadir' => '✓ Hadir',
                                                'izin'  => '📋 Izin',
                                                'sakit' => '🏥 Sakit',
                                                'alfa'  => '✗ Alfa',
                                            ])
                                            ->default('hadir')
                                            ->required()
                                            ->columnSpan(1),
                                        TextInput::make('keterangan')
                                            ->label('Keterangan')
                                            ->placeholder('Opsional')
                                            ->columnSpan(2),
                                    ])
                                    ->columns(5)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->defaultItems(0),
                            ]),
                    ])
                    ->skippable(false),
                ])
                ->action(function (array $data) {
                    if (empty($data['siswa_list'])) {
                        Notification::make()
                            ->title('Tidak ada siswa')
                            ->warning()->send();
                        return;
                    }

                    $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
                    $count = 0;

                    foreach ($data['siswa_list'] as $row) {
                        Absensi::updateOrCreate(
                            [
                                'siswa_id' => $row['siswa_id'],
                                'tanggal'  => $data['tanggal'],
                            ],
                            [
                                'kelas_id'        => $data['kelas_id'],
                                'tahun_ajaran_id' => $tahunAjaran?->id,
                                'status'          => $row['status'],
                                'keterangan'      => $row['keterangan'] ?? null,
                                'dicatat_oleh'    => auth()->id(),
                            ]
                        );
                        $count++;
                    }

                    Notification::make()
                        ->title("Absensi {$count} siswa berhasil disimpan!")
                        ->success()->send();

                    $this->tanggalFilter = $data['tanggal'];
                }),
        ];
    }
}