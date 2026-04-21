<?php

namespace App\Filament\Resources\ProductionCostingResource\Pages;

use App\Filament\Resources\ProductionCostingResource;
use App\Models\BomItem;
use App\Models\Formula;
use App\Models\Material;
use App\Models\ProductionOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Pages\CreateRecord;

class CreateProductionCosting extends CreateRecord
{
    protected static string $resource = ProductionCostingResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
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
            $nettoTarget = $bomItem->netto_target;
            $priceBom = $material->standart_cost ?? 0;
            $nettoProduksi = $data['netto_produksi'] ?? null;

            $productionOrder = ProductionOrder::create([
                'order_number' => $this->generateOrderNumber(),
                'formula_id' => $bomItem->formula_id,
                'qty_order' => 1,
                'start_date' => today(),
                'status' => 'In Progress',
                'operator_id' => auth()->id(),
            ]);

            $data['production_order_id'] = $productionOrder->id;
            $data['bom_item_id'] = $bomItem->id;
            $data['netto_target'] = $nettoTarget;
            $data['price_bom'] = $priceBom;
            $data['sub_price'] = $nettoTarget * $priceBom;
            $data['sub_cost_price'] = $nettoProduksi === null ? null : $nettoProduksi * $priceBom;
            unset($data['material_id']);

            return static::getModel()::create($data);
        });
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'TMB-' . now()->format('Ymd') . '-';
        $lastOrder = ProductionOrder::query()
            ->where('order_number', 'like', $prefix . '%')
            ->orderByDesc('order_number')
            ->first();

        $lastSequence = $lastOrder
            ? (int) str_replace($prefix, '', $lastOrder->order_number)
            : 0;

        return $prefix . str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
    }
}
