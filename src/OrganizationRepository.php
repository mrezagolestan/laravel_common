<?php

namespace Piod\LaravelCommon;


use Piod\LaravelCommon\Models\Piod\Organization;
use Illuminate\Support\Facades\Cache;

class OrganizationRepository
{
    private static $orgCAcheFlag = 'org:';


    public static function get($orgId)
    {
        return Cache::rememberForever(self::$orgCAcheFlag . $orgId, function () use ($orgId) {
            return (object)Organization::query()->select('id', 'name', 'picture')->find($orgId)->toArray();
        });
    }

    public static function getByCode($code)
    {
        return (object)Organization::query()->select('id', 'name', 'picture')->where('code', $code)->first()->toArray();
    }

    public static function reset($orgId)
    {
        Cache::forget(self::$orgCAcheFlag . $orgId);
        return self::get($orgId);
    }
}
