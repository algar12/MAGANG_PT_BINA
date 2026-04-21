<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang di-broadcast ke channel publik saat ESP32 mengirim data berat.
 * Channel: scale.{device_id}
 * React client subscribe ke channel ini untuk update live weight tanpa polling.
 */
class WeightReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $device_id,
        public readonly string $device_name,
        public readonly float  $weight,
        public readonly string $unit = 'KG',
    ) {}

    /**
     * Channel publik: scale.{device_id}
     * Contoh: scale.TIMBANGAN-01
     */
    public function broadcastOn(): array
    {
        return [new Channel('scale.' . $this->device_id)];
    }

    /**
     * Nama event yang didengar oleh React (Laravel Echo).
     */
    public function broadcastAs(): string
    {
        return 'weight.received';
    }

    /**
     * Data yang dikirim ke client.
     */
    public function broadcastWith(): array
    {
        return [
            'device_id'   => $this->device_id,
            'device_name' => $this->device_name,
            'weight'      => $this->weight,
            'unit'        => $this->unit,
            'timestamp'   => now()->toISOString(),
        ];
    }
}
