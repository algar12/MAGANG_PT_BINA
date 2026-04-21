<?php

namespace App\Filament\Exports;

use App\Models\Material;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class MaterialExporter extends BaseExporter
{
    protected static ?string $model = Material::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('kode_produk')->label('Kode Produk'),
            ExportColumn::make('nama_produk')->label('Nama Produk'),
            ExportColumn::make('uom_dasar')->label('UOM Dasar'),
            ExportColumn::make('standart_cost')->label('Standard Cost'),
            ExportColumn::make('is_active')
                ->label('Aktif')
                ->formatStateUsing(fn (bool $state): string => $state ? 'Ya' : 'Tidak'),
            ExportColumn::make('created_at')->label('Dibuat Pada'),
            ExportColumn::make('updated_at')->label('Diubah Pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export bahan baku selesai. ' . number_format($export->successful_rows) . ' baris berhasil diexport.';
    }
}
