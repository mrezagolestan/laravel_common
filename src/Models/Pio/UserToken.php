<?php

namespace Piod\LaravelCommon\Models\Pio;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    protected $guarded = [];

    protected $connection = "pio";
    protected $table = "user_tokens";

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
