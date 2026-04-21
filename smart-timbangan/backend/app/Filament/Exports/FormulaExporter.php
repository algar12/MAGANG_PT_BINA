<?php

namespace App\Filament\Exports;

use App\Models\Formula;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class FormulaExporter extends BaseExporter
{
    protected static ?string $model = Formula::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('formula_code')->label('Kode Formula'),
            ExportColumn::make('formula_name')->label('Nama Formula'),
            ExportColumn::make('mix_kategory')->label('Mix Kategori'),
            ExportColumn::make('status')
                ->label('Aktif')
                ->formatStateUsing(fn (bool $state): string => $state ? 'Ya' : 'Tidak'),
            ExportColumn::make('created_by')->label('Dibuat Oleh'),
            ExportColumn::make('created_at')->label('Dibuat Pada'),
            ExportColumn::make('updated_at')->label('Diubah Pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export formula selesai. ' . number_format($export->successful_rows) . ' baris berhasil diexport.';
    }
}
