<?php

namespace App\Filament\Exports;

use App\Models\Device;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class DeviceExporter extends BaseExporter
{
    protected static ?string $model = Device::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('device_id')->label('Device ID'),
            ExportColumn::make('name')->label('Nama'),
            ExportColumn::make('location')->label('Lokasi'),
            ExportColumn::make('is_active')
                ->label('Aktif')
                ->formatStateUsing(fn (bool $state): string => $state ? 'Ya' : 'Tidak'),
            ExportColumn::make('created_at')->label('Dibuat Pada'),
            ExportColumn::make('updated_at')->label('Diubah Pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export alat timbangan selesai. ' . number_format($export->successful_rows) . ' baris berhasil diexport.';
    }
}
