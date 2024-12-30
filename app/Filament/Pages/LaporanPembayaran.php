<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Pembayaran;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class LaporanPembayaran extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-report';
    protected static string $view = 'filament.pages.laporan-pembayaran';
    protected static ?string $title = 'Laporan Pembayaran';
    protected static ?string $navigationLabel = 'Laporan Pembayaran';
    protected static ?string $slug = 'laporan-pembayaran';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Report';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public function mount()
    {
        if (Auth::user()->role === 'Siswa') {
            abort(403, 'You do not have access to this page.');
        }
    }

    public function getData($startDate, $endDate)
    {
        return Pembayaran::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
