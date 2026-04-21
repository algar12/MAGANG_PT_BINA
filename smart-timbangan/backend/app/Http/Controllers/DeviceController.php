<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\ProductionCosting;
use Carbon\Carbon;

class DeviceController extends Controller
{
    public function storeWeight(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
            'weight' => 'required|numeric'
        ]);

        $device = Device::where('device_id', $validated['device_id'])->first();
        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found'], 404);
        }

        // Cari transaksi penimbangan yang sedang "Pending" untuk device ini
        $costing = ProductionCosting::where('device_id', $device->id)
                    ->where('status', 'Pending')
                    ->first();
        
        if ($costing) {
            $costing->update([
                'netto_produksi' => $validated['weight'],
                'sub_cost_price' => $validated['weight'] * $costing->price_bom,
                'status' => 'Weighed',
                'weighed_at' => Carbon::now()
            ]);
            return response()->json(['success' => true, 'message' => 'Saved to BOM']);
        }
        
        // Jika tidak ada BOM yang sedang ditimbang, simpan ke Cache untuk Live View
        cache()->put('scale_' . $device->device_id, $validated['weight'], now()->addMinutes(5));
        
        return response()->json(['success' => true, 'message' => 'Weight cached successfully']);
    }
}
