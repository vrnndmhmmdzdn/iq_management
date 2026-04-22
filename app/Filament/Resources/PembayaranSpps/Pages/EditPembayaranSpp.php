<?php

namespace App\Filament\Resources\PembayaranSpps\Pages;

use App\Filament\Resources\PembayaranSpps\PembayaranSppResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPembayaranSpp extends EditRecord
{
    protected static string $resource = PembayaranSppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
