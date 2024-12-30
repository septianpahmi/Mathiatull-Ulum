<?php

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePembayarans extends ManageRecords
{
    protected static string $resource = PembayaranResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public static function getWidgets(): array
    {
        return [
            \Filament\Widgets\StatsOverviewWidget::class,
        ];
    }
}
