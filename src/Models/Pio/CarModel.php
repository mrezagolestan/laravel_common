<?php

namespace Piod\LaravelCommon\Models\Pio;

use Illuminate\Database\Eloquent\Model;


class CarModel extends Model
{
    protected $connection = 'pio';

    protected $guarded = [];


    public function carBrand()
    {
        return $this->belongsTo(CarBrand::class);
    }
}
