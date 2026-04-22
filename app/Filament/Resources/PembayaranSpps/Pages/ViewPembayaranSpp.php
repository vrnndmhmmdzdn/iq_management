<?php

namespace App\Filament\Resources\PembayaranSpps\Pages;

use App\Filament\Resources\PembayaranSpps\PembayaranSppResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPembayaranSpp extends ViewRecord
{
    protected static string $resource = PembayaranSppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
