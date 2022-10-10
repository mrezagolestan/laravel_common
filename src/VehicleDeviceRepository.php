<?php

namespace Piod\LaravelCommon;

use Piod\LaravelCommon\Models\Pio\UserCar;
use Piod\LaravelCommon\Models\Piod\UserDevice;
use Illuminate\Support\Facades\Cache;
use Piod\LaravelCommon\Models\Piod\UserDeviceSetting;

class VehicleDeviceRepository
{
    private static $vdCAcheFlag = 'vd:';
    private static $vdsCAcheFlag = 'vds:';

    public static function get($vehicleDeviceId)
    {
        return Cache::rememberForever(self::$vdCAcheFlag . $vehicleDeviceId, function () use ($vehicleDeviceId) {
            $vd = UserDevice::query()->find($vehicleDeviceId);
            $userCar = UserCar::query()->select('car_model_id', 'car_brand_id', 'year', 'name')->with(['carModel' => function ($query) {
                $query->select('id', 'car_brand_id', 'name');
            }, 'carModel.carBrand' => function ($query) {
                $query->select('id', 'name');
            }])->find($vd->user_car_id);

            return (object)[
                'id' => $vehicleDeviceId,
                'organization_id' => $vd->organization_id,
                'user_id' => $vd->user_id,
                'created_at' => $vd->created_at,
                'ps_active' => $vd->ps_active,
                'vehicle' => (object)[
                    'brand_id' => $userCar?->carModel?->carBrand?->id,
                    'brand_name' => $userCar?->carModel?->carBrand?->name,
                    'model_id' => $userCar?->carModel?->id,
                    'model_name' => $userCar?->carModel?->name,
                    'year' => $userCar?->year,
                    'name' => $userCar?->name,
                    'vin' => $userCar?->vin,
                ]
            ];
        });
    }

    public static function delete($vehicleDeviceId)
    {
        Cache::forget(self::$vdCAcheFlag.$vehicleDeviceId);
    }

    public static function reset($vehicleDeviceId)
    {
        Cache::forget(self::$vdCAcheFlag . $vehicleDeviceId);
        return self::get($vehicleDeviceId);
    }

    public static function getSetting($vehicleDeviceId)
    {
        return Cache::rememberForever(self::$vdsCAcheFlag . $vehicleDeviceId, function () use ($vehicleDeviceId) {
            $vds = UserDeviceSetting::query()->where('user_device_id', $vehicleDeviceId)->first();
            return (object)[
                'voice_message' => $vds?->voice_message,
                'gps' => $vds?->gps,
                'si_robot' => $vds?->si_robot,
                'vpo_notif' => $vds->vpo_notif ?? 0,
            ];
        });
    }
    public static function deleteSetting($vehicleDeviceId)
    {
        Cache::forget(self::$vdsCAcheFlag . $vehicleDeviceId);
    }


    public static function resetSetting($vehicleDeviceId)
    {
        Cache::forget(self::$vdsCAcheFlag . $vehicleDeviceId);
        return self::getSetting($vehicleDeviceId);
    }

}
