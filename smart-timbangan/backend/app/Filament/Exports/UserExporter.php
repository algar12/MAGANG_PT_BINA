<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends BaseExporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('name')->label('Nama'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('email_verified_at')->label('Email Terverifikasi'),
            ExportColumn::make('created_at')->label('Dibuat Pada'),
            ExportColumn::make('updated_at')->label('Diubah Pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export user selesai. ' . number_format($export->successful_rows) . ' baris berhasil diexport.';
    }
}
