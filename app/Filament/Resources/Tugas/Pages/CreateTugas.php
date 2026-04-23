<?php

namespace App\Filament\Resources\Tugas\Pages;

use App\Filament\Resources\Tugas\TugasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTugas extends CreateRecord
{
    protected static string $resource = TugasResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}