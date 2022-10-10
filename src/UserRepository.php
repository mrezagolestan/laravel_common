<?php

namespace Piod\LaravelCommon;

use Piod\LaravelCommon\Models\Pio\User;
use Piod\LaravelCommon\Models\Pio\UserToken;
use Illuminate\Support\Facades\Cache;

class UserRepository
{
    private static $userCAcheFlag = 'u:';

    public static function get($userId)
    {
        return Cache::rememberForever(self::$userCAcheFlag.$userId, function () use ($userId) {
            return (object)User::query()->select('id','type','mobile','username','email','firstname','lastname','org_id')
                ->where('id', $userId)->first()->toArray();
        });
    }
    public static function delete($userId)
    {
        Cache::forget(self::$userCAcheFlag.$userId);
    }

    public static function getFCMToken($userId)
    {
        return User::query()->select('id','web_fcm_token')
            ->where('id', $userId)->first()?->web_fcm_token;
    }
    public static function reset($userId)
    {
        Cache::forget(self::$userCAcheFlag.$userId);
        return self::get($userId);
    }
    public static function checkAuthentication($token){
        return UserToken::query()->where('token',$token)->select('user_id')->first();
    }
}
