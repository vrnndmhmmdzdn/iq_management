<?php

namespace App\Filament\Resources\Jurnals\Pages;

use App\Filament\Resources\Jurnals\JurnalResource;
use App\Models\TahunAjaran;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateJurnal extends CreateRecord
{
    protected static string $resource = JurnalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['guru_id'] = auth()->user()->guru?->id;
        
        if (!$data['guru_id']) {
        Notification::make()
            ->title('Data guru tidak ditemukan')
            ->body('Akun ini belum terdaftar sebagai guru. Hubungi admin.')
            ->danger()
            ->send();

        throw new \Filament\Support\Exceptions\Halt();
        }

        // $data['guru_id'] = $guru->id;

        if (empty($data['tahun_ajaran_id'])) {
            $data['tahun_ajaran_id'] = TahunAjaran::where('is_aktif', true)->first()?->id;
        }

        if ($data['status'] === 'submitted') {
            $data['submitted_at'] = now();
        }

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        $status = $this->record->status;

        return Notification::make()
            ->title($status === 'submitted' ? 'Jurnal berhasil dikirim!' : 'Jurnal disimpan sebagai draft')
            ->body($status === 'submitted' ? 'Jurnal sudah dapat dilihat oleh admin.' : 'Kamu bisa mengedit dan mengirim nanti.')
            ->success();
    }
}