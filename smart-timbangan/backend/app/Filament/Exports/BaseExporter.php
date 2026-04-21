<?php

namespace App\Filament\Exports;

use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Exporter;

abstract class BaseExporter extends Exporter
{
    public function getFormats(): array
    {
        return [ExportFormat::Xlsx];
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }
}
