<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Siswa;
use App\Models\Pembayaran;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class DashboardStats extends BaseWidget
{
    protected function getCards(): array
    {
        $totalSiswa = Siswa::count();
        $totalUangMasuk = Pembayaran::where('status', 'Berhasil')->sum('nominal');
        $totalBelumDibayar = Pembayaran::where('status', 'Menunggu Pembayaran')->sum('nominal');
        $monthlyStudents = Siswa::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyStudents[$i] ?? 0;
        }
        $monthlyPembayaran = Pembayaran::selectRaw('MONTH(tgl_pembayaran) as month, COUNT(*) as total')
            ->whereYear('tgl_pembayaran', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $chartPembayaran = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartPembayaran[] = $monthlyPembayaran->get($i, 0);
        }

        $monthlyTagihan = Pembayaran::selectRaw('MONTH(tgl_tagihan) as month, COUNT(*) as total')
            ->whereYear('tgl_tagihan', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $chartTagihan = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartTagihan[] = $monthlyTagihan->get($i, 0);
        }
        return [
            Card::make('Jumlah Siswa', $totalSiswa)->extraAttributes(['class' => 'font-bold'])
                ->description('Total siswa terdaftar')
                ->color('primary')
                ->icon('heroicon-o-user')->chart($chartData),

            Card::make('Total Uang Masuk', 'Rp ' . number_format($totalUangMasuk, 0, ',', '.'))->extraAttributes(['class' => 'font-bold'])
                ->description('Pembayaran berhasil')
                ->color('success')
                ->icon('heroicon-o-credit-card')->chart($chartPembayaran),

            Card::make('Total Belum Dibayar', 'Rp ' . number_format($totalBelumDibayar, 0, ',', '.'))->extraAttributes(['class' => 'font-bold'])
                ->description('Menunggu pembayaran')
                ->color('danger')
                ->icon('heroicon-o-exclamation')->chart($chartTagihan),
        ];
    }
}
