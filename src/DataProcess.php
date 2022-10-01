<?php

namespace Piod\LaravelCommon;

use Illuminate\Support\Carbon;

class DataProcess
{

    public static function getTime($piodDeviceDateTime)
    {
        $createdAt = date('Y-m-d H:i:s');
        $storedAt = self::getStoredAtByOldMonthCorrection($piodDeviceDateTime, $createdAt);
        return (object)[
            'created_at' => $createdAt,
            'stored_at' => $storedAt,
        ];
    }

    //------------------------------------

    private static function getStoredAtByOldMonthCorrection($piodDeviceDateTime, $createdAt)
    {
        $pastTimeNormalizeThreshold = 2592000;// 30 * 24 * 60 * 60 = 2592000 second
        $futureTimeErrorCorrection = 1073;// this is piod device error when time is in future
        $now = Carbon::now();
        if ($piodDeviceDateTime != null) {
            $storedAtCarbon = Carbon::parse($piodDeviceDateTime, 'UTC')->timezone('Asia/Tehran');
            $storedAtDiffByNow = $storedAtCarbon->diffInSeconds($now, false);
            if ($storedAtDiffByNow < $pastTimeNormalizeThreshold && $storedAtDiffByNow >= 0) {
                //set Stored At if is in past until 1 month before
                return $storedAtCarbon->toDateTimeString();
            } else if ($storedAtDiffByNow < 0) {
                // set Stored At for future time
                $storedAtCarbon->subSeconds($futureTimeErrorCorrection);
                $storedAtDiffByNow = $storedAtCarbon->diffInSeconds($now, false);
                if ($storedAtDiffByNow > 0) {
                    return $storedAtCarbon->toDateTimeString();
                }
            }
        }
        return $createdAt;
    }


}