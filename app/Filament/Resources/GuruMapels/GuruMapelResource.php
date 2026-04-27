<?php

namespace App\Filament\Resources\GuruMapels;

use App\Filament\Resources\GuruMapels\Pages\CreateGuruMapel;
use App\Filament\Resources\GuruMapels\Pages\EditGuruMapel;
use App\Filament\Resources\GuruMapels\Pages\ListGuruMapels;
use App\Filament\Resources\GuruMapels\Schemas\GuruMapelForm;
use App\Filament\Resources\GuruMapels\Tables\GuruMapelsTable;
use App\Models\GuruMapel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GuruMapelResource extends Resource
{
    protected static ?string $model = GuruMapel::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel  = 'Guru Mata Pelajaran';
    protected static string |UnitEnum|null $navigationGroup  = 'Master Data';
    protected static ?int    $navigationSort   = 5;
    protected static ?string $modelLabel       = 'Guru Mapel';
    protected static ?string $pluralModelLabel = 'Guru Mata Pelajaran';

    public static function form(Schema $schema): Schema
    {
        return GuruMapelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuruMapelsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListGuruMapels::route('/'),
            'create' => CreateGuruMapel::route('/create'),
            'edit'   => EditGuruMapel::route('/{record}/edit'),
        ];
    }
}