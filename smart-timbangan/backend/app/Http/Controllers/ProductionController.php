<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionOrder;
use App\Models\ProductionCosting;

class ProductionController extends Controller
{
    public function showCosting($order_id)
    {
        $order = ProductionOrder::with('formula')->findOrFail($order_id);
        $costings = ProductionCosting::with(['bomItem.material'])->where('production_order_id', $order->id)->get();

        return view('production.costing', compact('order', 'costings'));
    }

    public function getLiveCosting($order_id)
    {
        // Dipanggil via AJAX setiap 2 detik untuk live update web
        $costings = ProductionCosting::where('production_order_id', $order_id)->get();
        return response()->json(['costings' => $costings]);
    }
}
