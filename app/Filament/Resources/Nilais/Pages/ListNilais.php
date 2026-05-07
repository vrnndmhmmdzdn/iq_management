<?php

namespace App\Filament\Resources\Nilais\Pages;

use App\Filament\Resources\Nilais\NilaiResource;
use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Builder;

class ListNilais extends ListRecords
{
    protected static string $resource = NilaiResource::class;

    
    protected function getTableQuery(): Builder
    {
        return Nilai::query()
            ->selectRaw('
                MIN(nilais.id) as id,
                nilais.kelas_id,
                nilais.mata_pelajaran_id,
                nilais.jenis,
                nilais.tanggal_ujian,
                ROUND(AVG(nilais.nilai), 2) as rata_rata,
                COUNT(*) as jumlah_siswa,
                kelas.nama_kelas,
                mata_pelajarans.nama as mapel_nama,
                users.name as guru_nama
            ')
            ->join('kelas', 'nilais.kelas_id', '=', 'kelas.id')
            ->join('mata_pelajarans', 'nilais.mata_pelajaran_id', '=', 'mata_pelajarans.id')
            ->leftJoin('guru_mapels', 'guru_mapels.mata_pelajaran_id', '=', 'nilais.mata_pelajaran_id')
            ->leftJoin('users', 'users.id', '=', 'guru_mapels.guru_id')
            ->groupBy(
                'nilais.kelas_id',
                'nilais.mata_pelajaran_id',
                'nilais.jenis',
                'nilais.tahun_ajaran_id',
                'nilais.tanggal_ujian',
                'kelas.nama_kelas',
                'mata_pelajarans.nama',
                'users.name'
            );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('input_massal')
                ->label('Input Nilai Kelas')
                ->icon('heroicon-o-academic-cap')
                ->color('primary')
                ->modalWidth('4xl')
                ->form([
                    Wizard::make([
                        Step::make('Pilih Kelas & Penilaian')
                        ->schema([
                            Select::make('tahun_ajaran_id')
                                ->label('Tahun Ajaran')
                                ->options(
                                    TahunAjaran::orderByDesc('is_aktif')->pluck('nama', 'id')
                                )
                                ->default(TahunAjaran::where('is_aktif', true)->first()?->id)
                                ->required(),

                            Select::make('kelas_id')
                                ->label('Kelas')
                                ->options(function () {
                                    $user  = auth()->user();
                                    $query = Kelas::query();
                                    if ($user->hasRole('guru')) {
                                        $query->where('wali_kelas_id', $user->id);
                                    }
                                    return $query->orderBy('nama_kelas')->pluck('nama_kelas', 'id');
                                })
                                ->required(),

                            Select::make('mata_pelajaran_id')
                                ->label('Mata Pelajaran')
                                ->relationship('mataPelajaran', 'nama')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('jenis')
                                ->label('Jenis Penilaian')
                                ->options([
                                    'harian' => 'Harian',
                                    'tugas'  => 'Tugas',
                                    'uts'    => 'UTS',
                                    'uas'    => 'UAS',
                                ])
                                ->required(),

                            \Filament\Forms\Components\DatePicker::make('tanggal_ujian')
                                ->label('Tanggal Ujian/Penilaian')
                                ->default(today())
                                ->required(),
                        ])
                        ->afterValidation(function (Set $set, $get) {
                            $kelasId       = $get('kelas_id');
                            $mapelId       = $get('mata_pelajaran_id');
                            $jenis         = $get('jenis');
                            $tahunAjaranId = $get('tahun_ajaran_id');
                            $tanggalUjian = $get('tanggal_ujian');

                            if (! $kelasId || ! $mapelId || ! $jenis || ! $tahunAjaranId || ! $tanggalUjian) return;

                            $sudahAda = Nilai::where('kelas_id', $kelasId)
                                ->where('mata_pelajaran_id', $mapelId)
                                ->where('jenis', $jenis)
                                ->where('tahun_ajaran_id', $tahunAjaranId)
                                ->where('tanggal_ujian', $tanggalUjian)
                                ->exists();

                            if ($sudahAda) {
                                Notification::make()
                                    ->title('Nilai sudah ada!')
                                    ->body('Nilai untuk kombinasi ini sudah pernah diinput. Klik row di tabel untuk melihat atau mengedit.')
                                    ->warning()
                                    ->send();
                                throw new Halt();
                            }

                            $siswas = Siswa::where('kelas_id', $kelasId)
                                ->whereNull('deleted_at')
                                ->orderBy('nama_lengkap')
                                ->get();

                            if ($siswas->isEmpty()) {
                                Notification::make()
                                    ->title('Tidak ada siswa di kelas ini')
                                    ->warning()->send();
                                throw new Halt();
                            }

                            $set('siswa_list', $siswas->map(fn($s) => [
                                'siswa_id'   => $s->id,
                                'nama'       => $s->nama_lengkap,
                                'nilai'      => '',
                                'keterangan' => '',
                            ])->toArray());
                        }),
                        Step::make('Input Nilai')
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
                                        TextInput::make('nilai')
                                            ->label('Nilai')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->placeholder('0-100')
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
                        Notification::make()->title('Tidak ada data siswa')->warning()->send();
                        return;
                    }

                    $count = 0;
                    foreach ($data['siswa_list'] as $row) {
                    Nilai::create([
                        'siswa_id'          => $row['siswa_id'],
                        'mata_pelajaran_id' => $data['mata_pelajaran_id'],
                        'jenis'             => $data['jenis'],
                        'tahun_ajaran_id'   => $data['tahun_ajaran_id'],
                        'kelas_id'          => $data['kelas_id'],
                        'nilai'             => ($row['nilai'] === '' || $row['nilai'] === null) ? 0 : $row['nilai'],
                        'tanggal_ujian'     => $data['tanggal_ujian'],
                        'keterangan'        => $row['keterangan'] ?? null,
                        'dicatat_oleh'      => auth()->id(),
                    ]);
                    $count++;
                }


                    Notification::make()
                        ->title("Nilai {$count} siswa berhasil disimpan!")
                        ->success()->send();
                }),
            ];
    }
}