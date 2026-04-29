<?php

namespace App\Filament\Resources\Jurnals\Schemas;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JurnalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Dasar')->columns(2)->schema([
                Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(
                        TahunAjaran::orderByDesc('is_aktif')
                            ->get()
                            ->mapWithKeys(fn($ta) => [$ta->id => $ta->nama . ($ta->is_aktif ? ' (Aktif)' : '')])
                    )
                    ->default(fn() => TahunAjaran::aktif()?->id)
                    ->required(),

                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->default(today())
                    ->required(),

                Select::make('kelas_id')
                    ->label('Kelas')
                    ->options(function () {
                        $user  = auth()->user();
                        $query = Kelas::query();
                        if ($user->hasRole('guru')) {
                            $query->where('wali_kelas_id', $user->id);
                        }
                        return $query->orderBy('nama_kelas')
                            ->get()
                            ->mapWithKeys(fn($k) => [$k->id => $k->nama_kelas]);
                    })
                    ->searchable()
                    ->required(),

                Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(
                        MataPelajaran::orderBy('nama')
                            ->get()
                            ->mapWithKeys(fn($m) => [$m->id => "[{$m->kode}] {$m->nama}"])
                    )
                    ->searchable()
                    ->required(),

                TextInput::make('pertemuan_ke')
                    ->label('Pertemuan Ke-')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft'     => 'Draft',
                        'submitted' => 'Kirim ke Admin',
                    ])
                    ->default('draft')
                    ->required(),
            ]),

            Section::make('Materi & Kegiatan')->schema([
                TextInput::make('materi')
                    ->label('Judul/Topik Materi')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('kompetensi_dasar')
                    ->label('Kompetensi Dasar (KD)')
                    ->placeholder('Contoh: 3.1 Memahami konsep...')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('indikator_pencapaian')
                    ->label('Indikator Pencapaian')
                    ->placeholder('Tuliskan indikator pencapaian kompetensi...')
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('deskripsi_kegiatan')
                    ->label('Deskripsi Kegiatan Pembelajaran')
                    ->placeholder('Uraikan kegiatan pembukaan, inti, dan penutup...')
                    ->rows(5)
                    ->required()
                    ->columnSpanFull(),
            ]),

            Section::make('Metode & Media')->columns(2)->schema([
                Select::make('metode_pembelajaran')
                    ->label('Metode Pembelajaran')
                    ->options([
                        'ceramah'     => 'Ceramah',
                        'diskusi'     => 'Diskusi',
                        'praktik'     => 'Praktik',
                        'demonstrasi' => 'Demonstrasi',
                        'tanya_jawab' => 'Tanya Jawab',
                        'penugasan'   => 'Penugasan',
                        'project'     => 'Project Based Learning',
                        'lainnya'     => 'Lainnya',
                    ])
                    ->required(),

                TextInput::make('media_pembelajaran')
                    ->label('Media Pembelajaran')
                    ->placeholder('Contoh: Proyektor, Modul, Video'),
            ]),

            Section::make('Waktu & Kehadiran')->columns(4)->schema([
                TimePicker::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->seconds(false)
                    ->required(),

                TimePicker::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->seconds(false)
                    ->required(),

                TextInput::make('jumlah_hadir')
                    ->label('Jumlah Hadir')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                TextInput::make('jumlah_tidak_hadir')
                    ->label('Jumlah Tidak Hadir')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
            ]),

            Section::make('Penilaian & Capaian')->columns(2)->schema([
                Select::make('capaian')
                    ->label('Capaian Pembelajaran')
                    ->options([
                        'tercapai' => 'Tercapai',
                        'sebagian' => 'Sebagian Tercapai',
                        'belum'    => 'Belum Tercapai',
                    ])
                    ->default('tercapai')
                    ->required(),

                Toggle::make('penilaian_dilakukan')
                    ->label('Ada Penilaian?')
                    ->default(false)
                    ->live()
                    ->columnSpan(1),

                Select::make('jenis_penilaian')
                    ->label('Jenis Penilaian')
                    ->options([
                        'tertulis'   => 'Tertulis',
                        'lisan'      => 'Lisan',
                        'praktik'    => 'Praktik',
                        'observasi'  => 'Observasi',
                        'portofolio' => 'Portofolio',
                    ])
                    ->visible(fn($get) => $get('penilaian_dilakukan'))
                    ->required(fn($get) => $get('penilaian_dilakukan')),
            ]),

            Section::make('Tindak Lanjut & Catatan')->schema([
                Textarea::make('tindak_lanjut')
                    ->label('Rencana Tindak Lanjut')
                    ->placeholder('Rencana kegiatan pertemuan berikutnya...')
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('catatan')
                    ->label('Catatan Tambahan')
                    ->placeholder('Kendala, temuan, atau hal lain yang perlu dicatat...')
                    ->rows(3)
                    ->columnSpanFull(),
            ]),

            Section::make('Lampiran')->schema([
                FileUpload::make('lampirans')
                    ->label('File Lampiran')
                    ->multiple()
                    ->directory('jurnal-lampiran')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                    ->maxSize(5120)
                    ->maxFiles(5)
                    ->helperText('Foto kegiatan, RPP, modul, dll. Maks 5 file, 5MB per file.'),
            ]),
        ]);
    }
}