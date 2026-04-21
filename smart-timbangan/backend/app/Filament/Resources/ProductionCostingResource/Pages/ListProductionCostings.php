<?php

namespace App\Filament\Resources\ProductionCostingResource\Pages;

use App\Filament\Resources\ProductionCostingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductionCostings extends ListRecords
{
    protected static string $resource = ProductionCostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
