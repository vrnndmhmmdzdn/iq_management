<?php

namespace App\Filament\Resources\Ekskuls\Pages;

use App\Filament\Resources\Ekskuls\EkskulResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEkskul extends EditRecord
{
    protected static string $resource = EkskulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
