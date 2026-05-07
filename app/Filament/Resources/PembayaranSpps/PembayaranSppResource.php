<?php

namespace App\Filament\Resources\PembayaranSpps;

use App\Filament\Resources\PembayaranSpps\Pages;
use App\Filament\Resources\PembayaranSpps\Schemas\PembayaranSppForm;
use App\Filament\Resources\PembayaranSpps\Schemas\PembayaranSppInfolist;
use App\Filament\Resources\PembayaranSpps\Tables\PembayaranSppsTable;
use App\Models\PembayaranSpp;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PembayaranSppResource extends Resource
{
    protected static ?string $model = PembayaranSpp::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Pembayaran SPP';
    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Pembayaran SPP';
    protected static ?string $pluralModelLabel = 'Pembayaran SPP';

    public static function form(Schema $form): Schema
    {
        return PembayaranSppForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return PembayaranSppsTable::configure($table);
    }

    public static function infolist(Schema $infolist): Schema
    {
        return PembayaranSppInfolist::configure($infolist);
    }

    public static function getPages(): array
    {
        return [
            'index'        => Pages\ListPembayaranSpps::route('/'),
            'view'         => Pages\ViewPembayaranSpp::route('/{record}'),
            'edit'         => Pages\EditPembayaranSpp::route('/{record}/edit'),
            'detail-kelas' => Pages\DetailSppKelas::route('/{kelas}/{periode}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return cache()->remember('badge_spp_menunggu', 60, fn() => 
            (string) static::getModel()::where('status', 'menunggu')->count() ?: null
        );
    }
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}