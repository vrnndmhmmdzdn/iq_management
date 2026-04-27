<?php

namespace App\Filament\Resources\Absensis;

use App\Filament\Resources\Absensis\Pages\CreateAbsensi;
use App\Filament\Resources\Absensis\Pages\DetailAbsensiKelas;
use App\Filament\Resources\Absensis\Pages\EditAbsensi;
use App\Filament\Resources\Absensis\Pages\ListAbsensis;
use App\Filament\Resources\Absensis\Schemas\AbsensiForm;
use App\Filament\Resources\Absensis\Tables\AbsensisTable;
use App\Models\Absensi;
use App\Models\Siswa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static ?string $navigationLabel  = 'Absensi Harian';
    protected static string|\UnitEnum|null $navigationGroup  = 'Akademik';
    protected static ?int    $navigationSort   = 1;
    protected static ?string $modelLabel       = 'Absensi';
    protected static ?string $pluralModelLabel = 'Absensi';

    public static function form(Schema $schema): Schema
    {
        return AbsensiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbsensisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()->hasRole('admin')) {
            $totalSiswa = Siswa::whereNull('deleted_at')->count();
            $sudahAbsen = Absensi::whereDate('tanggal', today())->count();
            $belum = $totalSiswa - $sudahAbsen;
            return $belum > 0 ? (string) $belum : null;
        }
        return null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAbsensis::route('/'),
            'create' => CreateAbsensi::route('/create'),
            'edit'   => EditAbsensi::route('/{record}/edit'),
            'detail' => DetailAbsensiKelas::route('/{kelas}/{tanggal}'),
        ];
    }
}