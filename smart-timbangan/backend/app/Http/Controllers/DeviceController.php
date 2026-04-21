<?php

namespace App\Http\Controllers;

use App\Events\CostingUpdated;
use App\Events\WeightReceived;
use App\Models\Device;
use App\Models\ProductionCosting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Terima data berat dari ESP32, simpan ke BOM Costing atau Cache,
     * lalu broadcast ke React client via WebSocket (Reverb).
     *
     * POST /api/sensor/weight
     * Body: { device_id, weight }
     */
    public function storeWeight(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
            'weight'    => 'required|numeric',
        ]);

        $device = Device::where('device_id', $validated['device_id'])->first();
        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found'], 404);
        }

        // ── 1. Broadcast berat live ke React (LiveWeightCard) ──────────────
        broadcast(new WeightReceived(
            device_id:   $device->device_id,
            device_name: $device->name,
            weight:      (float) $validated['weight'],
        ));

        // ── 2. Cek apakah ada BOM Pending untuk device ini ─────────────────
        $costing = ProductionCosting::where('device_id', $device->id)
                    ->where('status', 'Pending')
                    ->first();

        if ($costing) {
            // Simpan berat aktual ke costing
            $costing->update([
                'netto_produksi' => $validated['weight'],
                'sub_cost_price' => $validated['weight'] * $costing->price_bom,
                'status'         => 'Weighed',
                'weighed_at'     => Carbon::now(),
            ]);

            // ── 3. Broadcast update BOM ke React (BomTable) ────────────────
            broadcast(new CostingUpdated(
                production_order_id: $costing->production_order_id,
                costing:             $costing->fresh(),
            ));

            return response()->json(['success' => true, 'message' => 'Saved to BOM']);
        }

        // Jika tidak ada BOM Pending, simpan ke Cache sebagai fallback
        cache()->put('scale_' . $device->device_id, $validated['weight'], now()->addMinutes(5));

        return response()->json(['success' => true, 'message' => 'Weight cached successfully']);
    }

    /**
     * Kembalikan berat live dari cache (dipakai sebagai fallback jika WebSocket belum terhubung).
     *
     * GET /api/weight-live/{device_id}
     */
    public function getLiveWeight(string $device_id)
    {
        $weight = cache()->get('scale_' . $device_id);
        $device = Device::where('device_id', $device_id)->first();

        return response()->json([
            'device_id'   => $device_id,
            'device_name' => $device?->name ?? $device_id,
            'weight'      => $weight !== null ? (float) $weight : null,
            'unit'        => 'KG',
            'timestamp'   => now()->toISOString(),
        ]);
    }
}
