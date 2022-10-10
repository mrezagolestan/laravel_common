<?php

namespace Piod\LaravelCommon;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Event
{
    public static function set($type, $payload, $userId = null, $vehicleDeviceId = null)
    {
        try{
            $headers = [
                'source' => config('piod_common.events.source_name'),
                'type' => $type,
                'stored_at' => Carbon::parse(date('Y-m-d H:i:s'))->timestamp,
                'user_id' => $userId,
                'vehicle_device_id' => $vehicleDeviceId
            ];
            Rabbit::publish(config('piod_common.events.persistent'),$payload,config('piod_common.events.set_exchange_name'),'',$headers);
            $headers['payload'] = $payload;
            Log::info('Events:set:done',$headers);
            return true;
        }catch(\Exception $e){
            Log::error('Events:set:error',$e);
            return false;
        }

    }
}
