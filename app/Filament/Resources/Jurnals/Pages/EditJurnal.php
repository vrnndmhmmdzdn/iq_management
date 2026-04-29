<?php

namespace App\Filament\Resources\Jurnals\Pages;

use App\Filament\Resources\Jurnals\JurnalResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditJurnal extends EditRecord
{
    protected static string $resource = JurnalResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Guru tidak bisa edit jurnal yang sudah submitted
        if (
            auth()->user()->hasRole('guru') &&
            $this->record->status === 'submitted'
        ) {
            Notification::make()
                ->title('Jurnal sudah terkirim')
                ->body('Jurnal yang sudah dikirim tidak dapat diedit. Hubungi admin jika perlu perubahan.')
                ->warning()
                ->send();

            $this->redirect(JurnalResource::getUrl('view', ['record' => $this->record]));
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Set submitted_at saat pertama kali status berubah ke submitted
        if (
            $data['status'] === 'submitted' &&
            $this->record->status !== 'submitted'
        ) {
            $data['submitted_at'] = now();
        }

        return $data;
    }

    protected function getSavedNotification(): ?Notification
    {
        $status = $this->record->status;

        return Notification::make()
            ->title($status === 'submitted' ? 'Jurnal berhasil dikirim!' : 'Jurnal berhasil diperbarui')
            ->success();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn() => ! auth()->user()->hasRole('guru') || $this->record->status === 'draft'),
        ];
    }
}