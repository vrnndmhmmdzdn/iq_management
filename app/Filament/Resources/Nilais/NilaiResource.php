<?php

namespace App\Filament\Resources\Nilais;

use App\Filament\Resources\Nilais\Pages\DetailNilaiKelas;
use App\Filament\Resources\Nilais\Pages\EditNilai;
use App\Filament\Resources\Nilais\Pages\ListNilais;
use App\Filament\Resources\Nilais\Pages\ViewNilai;
use App\Filament\Resources\Nilais\Schemas\NilaiForm;
use App\Filament\Resources\Nilais\Tables\NilaisTable;
use App\Models\Nilai;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NilaiResource extends Resource
{
    protected static ?string $model = Nilai::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;
    protected static ?string $navigationLabel  = 'Input Nilai';
    protected static string|\UnitEnum|null $navigationGroup = 'Akademik';
    protected static ?int    $navigationSort   = 3;
    protected static ?string $modelLabel       = 'Nilai';
    protected static ?string $pluralModelLabel = 'Data Nilai';

    public static function form(Schema $schema): Schema
    {
        return NilaiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NilaisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListNilais::route('/'),
            'view'   => ViewNilai::route('/{record}'),
            'edit'   => EditNilai::route('/{record}/edit'),
            'detail' => DetailNilaiKelas::route('/{kelas}/{mapel}/{jenis}/{tanggal}'),
        ];
    }

public static function infolist(Schema $infolist): Schema
{
    return $infolist->components([
        Section::make('Data Siswa')->columns(2)->schema([
            TextEntry::make('siswa.nama_lengkap')->label('Nama Siswa'),
            TextEntry::make('kelas.nama_kelas')->label('Kelas'),
            TextEntry::make('siswa.nis')->label('NIS'),
        ]),
        Section::make('Detail Nilai')->columns(2)->schema([
            TextEntry::make('mataPelajaran.nama')->label('Mata Pelajaran'),
            TextEntry::make('jenis')
                ->label('Jenis Penilaian')
                ->formatStateUsing(fn($state) => match($state) {
                    'harian' => 'Harian', 'tugas' => 'Tugas',
                    'uts'    => 'UTS',    'uas'   => 'UAS',
                    default  => '-',
                })
                ->badge()
                ->color(fn($state) => match($state) {
                    'harian' => 'info', 'tugas' => 'warning',
                    'uts'    => 'primary', 'uas' => 'success',
                    default  => 'gray',
                }),
            TextEntry::make('nilai')
                ->label('Nilai')
                ->badge()
                ->color(fn($state) => match(true) {
                    $state >= 80 => 'success',
                    $state >= 60 => 'warning',
                    default      => 'danger',
                }),
            TextEntry::make('tahunAjaran.nama')->label('Tahun Ajaran'),
            TextEntry::make('keterangan')->label('Keterangan')->default('-')->columnSpanFull(),
        ]),
        Section::make('Info Pencatatan')->columns(2)->schema([
            TextEntry::make('pencatat.name')->label('Dicatat Oleh'),
            TextEntry::make('created_at')->label('Dibuat')->dateTime('d M Y H:i'),
            TextEntry::make('updated_at')->label('Diperbarui')->dateTime('d M Y H:i'),
        ]),
    ]);
}
}