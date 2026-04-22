<?php

namespace App\Filament\Resources\PembayaranSpps;

use App\Filament\Resources\PembayaranSpps\Pages\CreatePembayaranSpp;
use App\Filament\Resources\PembayaranSpps\Pages\EditPembayaranSpp;
use App\Filament\Resources\PembayaranSpps\Pages\ListPembayaranSpps;
use App\Filament\Resources\PembayaranSpps\Pages\ViewPembayaranSpp;
use App\Filament\Resources\PembayaranSpps\Schemas\PembayaranSppForm;
use App\Filament\Resources\PembayaranSpps\Schemas\PembayaranSppInfolist;
use App\Filament\Resources\PembayaranSpps\Tables\PembayaranSppsTable;
use App\Models\PembayaranSpp;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PembayaranSppResource extends Resource
{
    protected static ?string $model = PembayaranSpp::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'periode';

    public static function form(Schema $schema): Schema
    {
        return PembayaranSppForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PembayaranSppInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PembayaranSppsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPembayaranSpps::route('/'),
            'create' => CreatePembayaranSpp::route('/create'),
            'view' => ViewPembayaranSpp::route('/{record}'),
            'edit' => EditPembayaranSpp::route('/{record}/edit'),
        ];
    }
}
