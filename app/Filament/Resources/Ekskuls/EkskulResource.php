<?php

namespace App\Filament\Resources\Ekskuls;

use App\Filament\Resources\Ekskuls\Pages\CreateEkskul;
use App\Filament\Resources\Ekskuls\Pages\EditEkskul;
use App\Filament\Resources\Ekskuls\Pages\ListEkskuls;
use App\Filament\Resources\Ekskuls\Schemas\EkskulForm;
use App\Filament\Resources\Ekskuls\Tables\EkskulsTable;
use App\Models\Ekskul;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EkskulResource extends Resource
{
    protected static ?string $model = Ekskul::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'ekskul';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';
    
    public static function form(Schema $schema): Schema
    {
        return EkskulForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EkskulsTable::configure($table);
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
            'index' => ListEkskuls::route('/'),
            'create' => CreateEkskul::route('/create'),
            'edit' => EditEkskul::route('/{record}/edit'),
        ];
    }
}
