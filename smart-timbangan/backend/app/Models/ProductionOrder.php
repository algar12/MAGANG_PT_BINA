<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    protected $guarded = [];

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function productionCostings()
    {
        return $this->hasMany(ProductionCosting::class);
    }
}
