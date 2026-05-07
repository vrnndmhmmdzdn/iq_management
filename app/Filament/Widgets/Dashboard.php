<?php

namespace App\Filament\Widgets;

use App\Models\PembayaranSpp;
use Filament\Widgets\ChartWidget;

class Dashboard extends ChartWidget
{
    protected ?string $heading = 'Dashboard';

    protected function getData(): array
    {
        // Ambil data per bulan (1-12)
        $data = PembayaranSpp::where('status', 'dikonfirmasi')
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Isi bulan yang kosong dengan angka 0 agar grafik tidak patah
        $formattedData = [];
        foreach (range(1, 12) as $month) {
            $formattedData[] = $data[$month] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pembayaran SPP',
                    'data' => $formattedData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
