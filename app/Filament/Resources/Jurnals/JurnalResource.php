<?php

namespace App\Filament\Resources\Jurnals;

use App\Filament\Resources\Jurnals\Pages\CreateJurnal;
use App\Filament\Resources\Jurnals\Pages\DetailJurnalGuru;
use App\Filament\Resources\Jurnals\Pages\EditJurnal;
use App\Filament\Resources\Jurnals\Pages\ListJurnals;
use App\Filament\Resources\Jurnals\Pages\ViewJurnal;
use App\Filament\Resources\Jurnals\Schemas\JurnalForm;
use App\Filament\Resources\Jurnals\Tables\JurnalsTable;
use App\Models\JurnalGuru;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JurnalResource extends Resource
{
    protected static ?string $model = JurnalGuru::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;
    protected static ?string $navigationLabel  = 'Jurnal Guru';
    protected static string|\UnitEnum|null $navigationGroup = 'Akademik';
    protected static ?int    $navigationSort   = 4;
    protected static ?string $modelLabel       = 'Jurnal';
    protected static ?string $pluralModelLabel = 'Jurnal Guru';

    public static function form(Schema $schema): Schema
    {
        return JurnalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JurnalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListJurnals::route('/'),
            'create' => CreateJurnal::route('/create'),
            'view'   => ViewJurnal::route('/{record}'),
            'edit'   => EditJurnal::route('/{record}/edit'),
            'detail' => DetailJurnalGuru::route('/{guru}/{tanggal}'),
        ];
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist->components([
            Section::make('Informasi Dasar')->columns(3)->schema([
                TextEntry::make('guru.nama_lengkap')->label('Guru'),
                TextEntry::make('kelas.nama_kelas')->label('Kelas'),
                TextEntry::make('mataPelajaran.nama')->label('Mata Pelajaran'),
                TextEntry::make('tahunAjaran.nama')->label('Tahun Ajaran'),
                TextEntry::make('tanggal')->label('Tanggal')->date('l, d F Y'),
                TextEntry::make('pertemuan_ke')->label('Pertemuan Ke-'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'draft'     => 'Draft',
                        'submitted' => 'Terkirim',
                        default     => '-',
                    })
                    ->color(fn($state) => match($state) {
                        'draft'     => 'gray',
                        'submitted' => 'success',
                        default     => 'gray',
                    }),
                TextEntry::make('submitted_at')
                    ->label('Dikirim Pada')
                    ->dateTime('d M Y H:i')
                    ->default('-'),
            ]),

            Section::make('Materi & Kegiatan')->schema([
                TextEntry::make('materi')->label('Topik Materi')->columnSpanFull(),
                TextEntry::make('kompetensi_dasar')->label('Kompetensi Dasar')->columnSpanFull(),
                TextEntry::make('indikator_pencapaian')->label('Indikator Pencapaian')->default('-')->columnSpanFull(),
                TextEntry::make('deskripsi_kegiatan')->label('Deskripsi Kegiatan')->columnSpanFull(),
            ]),

            Section::make('Metode, Waktu & Kehadiran')->columns(3)->schema([
                TextEntry::make('metode_pembelajaran')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'ceramah'     => 'Ceramah',    'diskusi'     => 'Diskusi',
                        'praktik'     => 'Praktik',    'demonstrasi' => 'Demonstrasi',
                        'tanya_jawab' => 'Tanya Jawab','penugasan'   => 'Penugasan',
                        'project'     => 'Project Based Learning',
                        'lainnya'     => 'Lainnya',    default       => '-',
                    })
                    ->color('info'),
                TextEntry::make('media_pembelajaran')->label('Media')->default('-'),
                TextEntry::make('durasi')->label('Durasi')->state(fn($record) => $record->durasi),
                TextEntry::make('jam_mulai')->label('Jam Mulai'),
                TextEntry::make('jam_selesai')->label('Jam Selesai'),
                TextEntry::make('jumlah_hadir')->label('Hadir')->suffix(' siswa'),
                TextEntry::make('jumlah_tidak_hadir')->label('Tidak Hadir')->suffix(' siswa'),
            ]),

            Section::make('Penilaian & Capaian')->columns(3)->schema([
                TextEntry::make('capaian')
                    ->label('Capaian Pembelajaran')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'tercapai' => 'Tercapai', 'sebagian' => 'Sebagian Tercapai',
                        'belum'    => 'Belum Tercapai', default => '-',
                    })
                    ->color(fn($state) => match($state) {
                        'tercapai' => 'success', 'sebagian' => 'warning',
                        'belum'    => 'danger',  default    => 'gray',
                    }),
                IconEntry::make('penilaian_dilakukan')->label('Ada Penilaian?')->boolean(),
                TextEntry::make('jenis_penilaian')
                    ->label('Jenis Penilaian')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'tertulis'   => 'Tertulis',   'lisan'      => 'Lisan',
                        'praktik'    => 'Praktik',    'observasi'  => 'Observasi',
                        'portofolio' => 'Portofolio', default      => '-',
                    })
                    ->color('primary')
                    ->default('-'),
            ]),

            Section::make('Tindak Lanjut & Catatan')->schema([
                TextEntry::make('tindak_lanjut')->label('Rencana Tindak Lanjut')->default('-')->columnSpanFull(),
                TextEntry::make('catatan')->label('Catatan Tambahan')->default('-')->columnSpanFull(),
            ]),

            Section::make('Info Pencatatan')->columns(2)->schema([
                TextEntry::make('created_at')->label('Dibuat')->dateTime('d M Y H:i'),
                TextEntry::make('updated_at')->label('Diperbarui')->dateTime('d M Y H:i'),
            ]),
        ]);
    }
}