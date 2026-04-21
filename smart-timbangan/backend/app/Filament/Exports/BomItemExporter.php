<?php

namespace App\Filament\Exports;

use App\Models\BomItem;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class BomItemExporter extends BaseExporter
{
    protected static ?string $model = BomItem::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('material.nama_produk')->label('Bahan Baku'),
            ExportColumn::make('netto_target')->label('Netto Target'),
            ExportColumn::make('bom_konversi_uom')->label('Satuan'),
            ExportColumn::make('is_optional')
                ->label('Opsional')
                ->formatStateUsing(fn (bool $state): string => $state ? 'Ya' : 'Tidak'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export BOM Item selesai. ' . number_format($export->successful_rows) . ' baris berhasil diexport.';
    }
}
