<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JurnalReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $tanggal,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Pengingat Jurnal Harian',
            'body'  => "Kamu belum mengisi jurnal untuk hari ini ({$this->tanggal}). Segera isi sebelum hari berakhir.",
            'icon'  => 'heroicon-o-book-open',
            'color' => 'warning',
            'actions' => [
                [
                    'label' => 'Tulis Jurnal',
                    'url'   => url('/admin/jurnals/create'),
                ],
            ],
        ];
    }
}