<?php

namespace App\Events;

use App\Models\ProductionCosting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang di-broadcast saat status BOM item berubah (weight disimpan ke costing).
 * Channel: production.{order_id}
 * React client subscribe ke channel ini untuk update tabel BOM tanpa polling.
 */
class CostingUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int              $production_order_id,
        public readonly ProductionCosting $costing,
    ) {}

    /**
     * Channel publik: production.{order_id}
     */
    public function broadcastOn(): array
    {
        return [new Channel('production.' . $this->production_order_id)];
    }

    /**
     * Nama event yang didengar oleh React.
     */
    public function broadcastAs(): string
    {
        return 'costing.updated';
    }

    /**
     * Data costing terbaru yang dikirim ke client.
     */
    public function broadcastWith(): array
    {
        return [
            'production_order_id' => $this->production_order_id,
            'costing'             => [
                'id'              => $this->costing->id,
                'bom_item_id'     => $this->costing->bom_item_id,
                'netto_produksi'  => $this->costing->netto_produksi,
                'sub_cost_price'  => $this->costing->sub_cost_price,
                'status'          => $this->costing->status,
                'weighed_at'      => $this->costing->weighed_at,
            ],
            'timestamp' => now()->toISOString(),
        ];
    }
}
