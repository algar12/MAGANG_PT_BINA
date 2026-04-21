<?php

namespace App\Filament\Exports;

use App\Models\ProductionCosting;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class ProductionCostingExporter extends BaseExporter
{
    protected static ?string $model = ProductionCosting::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('productionOrder.order_number')->label('Nomor'),
            ExportColumn::make('bomItem.material.nama_produk')->label('Nama Produk'),
            ExportColumn::make('netto_produksi')->label('Netto Aktual (Live)'),
            ExportColumn::make('uom_display')->label('UOM'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('weighed_at')->label('Ditimbang Pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export penimbangan selesai. ' . number_format($export->successful_rows) . ' baris berhasil diexport.';
    }
}
