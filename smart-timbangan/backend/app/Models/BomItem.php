<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    protected $guarded = [];

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
