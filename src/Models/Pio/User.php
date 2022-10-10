<?php

namespace Piod\LaravelCommon\Models\Pio;

use App\Models\Piod\UserDevice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $connection = 'pio';

    protected $table = "users";

    protected $guarded = [];

    public function userDevices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function userCars()
    {
        return $this->hasMany(UserCar::class);
    }
}
