<?php

namespace App\Filament\Widgets;

use App\Models\Material;
use App\Models\Formula;
use App\Models\Device;
use App\Models\ProductionOrder;
use App\Models\ProductionCosting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '2s';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Bahan Baku', Material::count())
                ->description('Bahan yang terdaftar')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),
            
            Stat::make('Total Resep', Formula::count())
                ->description('Formula aktif')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('success'),

            Stat::make('Alat Terhubung', Device::count())
                ->description('Unit ESP32')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('warning'),

            Stat::make('Order In Progress', ProductionOrder::where('status', 'In Progress')->count())
                ->description('Sedang diproduksi')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),
            
            Stat::make('Timbangan Pending', ProductionCosting::where('status', 'Pending')->count())
                ->description('Menunggu ditimbang')
                ->descriptionIcon('heroicon-m-scale')
                ->color('danger'),
        ];
    }
}
