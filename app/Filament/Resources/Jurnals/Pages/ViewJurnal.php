<?php

namespace App\Filament\Resources\Jurnals\Pages;

use App\Filament\Resources\Jurnals\JurnalResource;
use App\Models\JurnalKomentar;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewJurnal extends ViewRecord
{
    protected static string $resource = JurnalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn() =>
                    ! auth()->user()->hasRole('guru') ||
                    $this->record->status === 'draft'
                ),

            Action::make('submit_jurnal')
                ->label('Kirim ke Admin')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Kirim Jurnal?')
                ->modalDescription('Setelah dikirim, jurnal tidak dapat diedit. Pastikan semua data sudah benar.')
                ->visible(fn() =>
                    auth()->user()->hasRole('guru') &&
                    $this->record->status === 'draft'
                )
                ->action(function () {
                    $this->record->update([
                        'status'       => 'submitted',
                        'submitted_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Jurnal berhasil dikirim!')
                        ->body('Admin dapat memantau jurnal kamu sekarang.')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'submitted_at']);
                }),

            Action::make('tambah_komentar')
                ->label('Tambah Komentar')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('info')
                ->visible(fn() => ! auth()->user()->hasRole('guru'))
                ->modalWidth('lg')
                ->modalHeading('Tambah Komentar / Feedback')
                ->form([
                    Textarea::make('komentar')
                        ->label('Komentar')
                        ->rows(4)
                        ->required(),
                ])
                ->action(function (array $data) {
                    JurnalKomentar::create([
                        'jurnal_guru_id' => $this->record->id,
                        'user_id'        => auth()->id(),
                        'komentar'       => $data['komentar'],
                    ]);

                    Notification::make()
                        ->title('Komentar berhasil ditambahkan')
                        ->success()
                        ->send();
                }),

            Action::make('kembali')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(JurnalResource::getUrl('index')),
        ];
    }
}