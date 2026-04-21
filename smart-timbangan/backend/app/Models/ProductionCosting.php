<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionCosting extends Model
{
    protected $guarded = [];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function bomItem()
    {
        return $this->belongsTo(BomItem::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function getUomDisplayAttribute(): string
    {
        return 'KG';
    }
}
