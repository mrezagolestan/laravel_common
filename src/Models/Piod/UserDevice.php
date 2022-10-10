<?php

namespace Piod\LaravelCommon\Models\Piod;

use App\Models\Pio\User;
use App\Models\Pio\UserCar;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $guarded = [];

    protected $connection = "piod";


    public function user()
    {
        return $this->setConnection('pio')->belongsTo(User::class);
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class);

    }

    public function userCar()
    {
        return $this->setConnection('pio')->belongsTo(UserCar::class);
    }

}
