<?php

namespace App\Filament\Exports;

use App\Models\ProductionOrder;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class ProductionOrderExporter extends BaseExporter
{
    protected static ?string $model = ProductionOrder::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('order_number')->label('Nomor Order'),
            ExportColumn::make('formula.formula_name')->label('Formula'),
            ExportColumn::make('qty_order')->label('Qty Order'),
            ExportColumn::make('start_date')->label('Tanggal Mulai'),
            ExportColumn::make('end_date')->label('Tanggal Selesai'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('operator.name')->label('Operator'),
            ExportColumn::make('created_at')->label('Dibuat Pada'),
            ExportColumn::make('updated_at')->label('Diubah Pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export rencana kerja selesai. ' . number_format($export->successful_rows) . ' baris berhasil diexport.';
    }
}
