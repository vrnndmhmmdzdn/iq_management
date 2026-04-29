<?php

namespace App\Filament\Resources\Ekskuls\Pages;

use App\Filament\Resources\Ekskuls\EkskulResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEkskuls extends ListRecords
{
    protected static string $resource = EkskulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
