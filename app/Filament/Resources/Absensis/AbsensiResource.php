<?php

namespace App\Filament\Resources\Absensis;

use App\Filament\Resources\Absensis\Pages\CreateAbsensi;
use App\Filament\Resources\Absensis\Pages\InputAbsensi;
use App\Filament\Resources\Absensis\Pages\ListAbsensis;
use App\Filament\Resources\Absensis\Pages\RekapAbsensi;
use App\Models\Absensi;
use Filament\Resources\Resource;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;
    protected static string|\BackedEnum|null $navigationIcon    = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel   = 'Absensi Harian';
    protected static string|\UnitEnum|null $navigationGroup   = 'Akademik';
    protected static ?int    $navigationSort    = 1;
    protected static ?string $modelLabel        = 'Absensi';
    protected static ?string $pluralModelLabel  = 'Absensi';

    public static function getNavigationBadge(): ?string
    {
        // Badge jumlah siswa belum diabsen hari ini (hanya untuk admin)
        if (auth()->user()->hasRole('admin')) {
            $totalSiswa = \App\Models\Siswa::whereNull('deleted_at')->count();
            $sudahAbsen = Absensi::hariIni()->count();
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
            'index' => InputAbsensi::route('/'),
            'rekap' => RekapAbsensi::route('/rekap'),
        ];
    }
}