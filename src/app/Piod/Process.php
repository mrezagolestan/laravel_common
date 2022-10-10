<?php

namespace App\Piod;

class Process
{
    public static function handle($body,$headers){
        dd($body);
        return true;//true = ack message    ,  false = nack message
    }
}