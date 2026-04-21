<?php

namespace App\Filament\Resources\ProductionCostingResource\Pages;

use App\Filament\Resources\ProductionCostingResource;
use App\Models\BomItem;
use App\Models\Formula;
use App\Models\Material;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductionCosting extends EditRecord
{
    protected static string $resource = ProductionCostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! array_key_exists('material_id', $data)) {
            return $data;
        }

        $material = Material::findOrFail($data['material_id']);
        $formula = Formula::firstOrCreate(
            ['formula_code' => 'BAHAN-BAKU-DEFAULT'],
            [
                'formula_name' => 'Bahan Baku Default',
                'status' => true,
                'created_by' => auth()->id(),
            ],
        );
        $bomItem = BomItem::firstOrCreate(
            [
                'formula_id' => $formula->id,
                'material_id' => $material->id,
            ],
            [
                'bom_konversi_qty' => 1,
                'bom_konversi_uom' => 'KG',
                'netto_target' => 0,
                'mix_id' => null,
                'is_optional' => false,
                'created_by' => auth()->id(),
            ],
        );

        $data['bom_item_id'] = $bomItem->id;
        $data['netto_target'] = $bomItem->netto_target;
        $data['price_bom'] = $material->standart_cost ?? 0;
        $data['sub_price'] = $data['netto_target'] * $data['price_bom'];
        $data['sub_cost_price'] = ($data['netto_produksi'] ?? null) === null
            ? null
            : $data['netto_produksi'] * $data['price_bom'];

        unset($data['material_id']);

        return $data;
    }
}
