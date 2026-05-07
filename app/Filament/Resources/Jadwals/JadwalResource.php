<?php

namespace App\Filament\Resources\Jadwals;

use App\Filament\Resources\Jadwals\Pages\CreateJadwal;
use App\Filament\Resources\Jadwals\Pages\DetailJadwalKelas;
use App\Filament\Resources\Jadwals\Pages\EditJadwal;
use App\Filament\Resources\Jadwals\Pages\ListJadwals;
use App\Filament\Resources\Jadwals\Pages\ViewJadwal;
use App\Filament\Resources\Jadwals\Schemas\JadwalForm;
use App\Filament\Resources\Jadwals\Tables\JadwalsTable;
use App\Models\JadwalPelajaran;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JadwalResource extends Resource
{
    protected static ?string $model = JadwalPelajaran::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    protected static ?string $navigationLabel  = 'Jadwal Pelajaran';
    protected static string|\UnitEnum|null $navigationGroup = 'Akademik';
    protected static ?int    $navigationSort   = 5;
    protected static ?string $modelLabel       = 'Jadwal';
    protected static ?string $pluralModelLabel = 'Jadwal Pelajaran';

    public static function form(Schema $schema): Schema
    {
        return JadwalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListJadwals::route('/'),
            'create' => CreateJadwal::route('/create'),
            'view'   => ViewJadwal::route('/{record}'),
            'edit'   => EditJadwal::route('/{record}/edit'),
            'detail' => DetailJadwalKelas::route('/{kelas}/{hari}'),
        ];
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist->components([
            Section::make('Detail Jadwal')->columns(2)->schema([
                TextEntry::make('tahunAjaran.nama')
                    ->label('Tahun Ajaran'),

                TextEntry::make('hari')
                    ->label('Hari')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'senin'  => 'Senin',  'selasa' => 'Selasa',
                        'rabu'   => 'Rabu',   'kamis'  => 'Kamis',
                        'jumat'  => 'Jumat',  'sabtu'  => 'Sabtu',
                        default  => '-',
                    })
                    ->color('info'),

                TextEntry::make('guru.nama_lengkap')
                    ->label('Guru'),

                TextEntry::make('kelas.nama_kelas')
                    ->label('Kelas'),

                TextEntry::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran'),

                TextEntry::make('jam')
                    ->label('Jam')
                    ->state(fn($record) =>
                        \Carbon\Carbon::parse($record->jam_mulai)->format('H:i') . ' – ' .
                        \Carbon\Carbon::parse($record->jam_selesai)->format('H:i')
                    ),
            ]),

            Section::make('Info Pencatatan')->columns(2)->schema([
                TextEntry::make('created_at')->label('Dibuat')->dateTime('d M Y H:i'),
                TextEntry::make('updated_at')->label('Diperbarui')->dateTime('d M Y H:i'),
            ]),
        ]);
    }
}   