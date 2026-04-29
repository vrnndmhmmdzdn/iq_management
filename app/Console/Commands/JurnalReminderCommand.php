<?php

namespace App\Console\Commands;

use App\Models\Guru;
use App\Models\JurnalGuru;
use App\Models\TahunAjaran;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class JurnalReminderCommand extends Command
{
    protected $signature   = 'jurnal:reminder';
    protected $description = 'Kirim notifikasi reminder ke guru yang belum mengisi jurnal hari ini';

    public function handle(): void
    {
        $hari        = now()->locale('id')->translatedFormat('l, d F Y');
        $hariEnum    = strtolower(now()->locale('id')->translatedFormat('l'));
        $tanggalHari = now()->toDateString();
        $tahunAjaranId = TahunAjaran::where('is_aktif', true)->first()?->id;

        // Guru yang punya jadwal hari ini
        $guruAdaJadwal = Guru::where('status', 'aktif')
            ->whereHas('jadwals', fn($q) => $q
                ->where('hari', $hariEnum)
                ->where('tahun_ajaran_id', $tahunAjaranId)
            )
            ->with('user')
            ->get();

        // Guru yang sudah isi jurnal hari ini
        $sudahIsi = JurnalGuru::whereDate('tanggal', $tanggalHari)
            ->pluck('guru_id')
            ->unique();

        // Guru yang belum isi
        $guruBelumIsi = $guruAdaJadwal->whereNotIn('id', $sudahIsi);

        foreach ($guruBelumIsi as $guru) {
            // Notifikasi dikirim ke User milik guru
            Notification::make()
                ->title('Pengingat Jurnal Harian')
                ->body("Kamu belum mengisi jurnal untuk hari ini ({$hari}). Segera isi sebelum hari berakhir.")
                ->warning()
                ->actions([
                    Action::make('tulis')
                        ->label('Tulis Jurnal')
                        ->button()
                        ->url(url('/admin/jurnals/create'))
                        ->markAsRead(),
                ])
                ->sendToDatabase($guru->user, isEventDispatched: true);
        }

        $this->info("Reminder dikirim ke {$guruBelumIsi->count()} guru.");
    }
}