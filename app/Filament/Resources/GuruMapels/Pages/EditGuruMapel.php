<?php

namespace App\Filament\Resources\GuruMapels\Pages;

use App\Filament\Resources\GuruMapels\GuruMapelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGuruMapel extends EditRecord
{
    protected static string $resource = GuruMapelResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}