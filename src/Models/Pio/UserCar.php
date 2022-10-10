<?php

namespace Piod\LaravelCommon\Models\Pio;

use Illuminate\Database\Eloquent\Model;


class UserCar extends Model
{
    protected $connection = "pio";

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function caBrand()
    {
        return $this->belongsTo(CarBrand::class);
    }

    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }
}
