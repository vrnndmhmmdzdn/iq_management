<?php

namespace App\Filament\Resources\PembayaranSpps\Pages;

use App\Filament\Resources\PembayaranSpps\PembayaranSppResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPembayaranSpps extends ListRecords
{
    protected static string $resource = PembayaranSppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
