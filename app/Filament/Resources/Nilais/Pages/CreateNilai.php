<?php

namespace App\Filament\Resources\Nilais\Pages;

use App\Filament\Resources\Nilais\NilaiResource;
use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateNilai extends CreateRecord
{
    protected static string $resource = NilaiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dicatat_oleh'] = auth()->id();
        if (empty($data['tahun_ajaran_id'])) {
            $data['tahun_ajaran_id'] = TahunAjaran::where('is_aktif', true)->first()?->id;
        }
        return $data;
    }
}