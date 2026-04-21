<?php

namespace App\Filament\Resources\BomItemResource\Pages;

use App\Filament\Resources\BomItemResource;
use App\Models\Formula;
use Filament\Resources\Pages\CreateRecord;

class CreateBomItem extends CreateRecord
{
    protected static string $resource = BomItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $formula = Formula::firstOrCreate(
            ['formula_code' => 'BOM-DEFAULT'],
            [
                'formula_name' => 'BOM Default',
                'status' => true,
                'created_by' => auth()->id(),
            ],
        );

        $data['formula_id'] = $formula->id;
        $data['bom_konversi_qty'] = 1;
        $data['bom_konversi_uom'] = $data['bom_konversi_uom'] ?? 'KG';
        $data['mix_id'] = null;
        $data['created_by'] = auth()->id();

        return $data;
    }
}
