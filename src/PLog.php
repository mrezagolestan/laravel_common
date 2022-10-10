<?php

namespace Piod\LaravelCommon;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PLog
{
    public static bool $dbQueryEnable = false;

    public static function dbQueryEnable(){
        DB::enableQueryLog();
        self::$dbQueryEnable = true;
    }

    public static function debug($message, $context = [], $sourceTag = '', $traceInclude = true)
    {
        return self::log('debug', $message, $context, $sourceTag, $traceInclude);
    }

    public static function info($message, $context = [], $sourceTag = '', $traceInclude = true)
    {
        return self::log('info', $message, $context, $sourceTag, $traceInclude);
    }

    public static function notice($message, $context = [], $sourceTag = '', $traceInclude = true)
    {
        return self::exception('notice', $message, $context, $sourceTag, $traceInclude);
    }

    public static function warning($error, $context = [], $sourceTag = '')
    {
        return self::exception('warning', $error, $context, $sourceTag);
    }

    public static function error($error, $context = [], $sourceTag = '')
    {
        return self::exception('error', $error, $context, $sourceTag);
    }

    public static function critical($error, $context = [], $sourceTag = '')
    {
        return self::exception('critical', $error, $context, $sourceTag);
    }

    public static function alert($error, $context = [], $sourceTag = '')
    {
        return self::exception('alert', $error, $context, $sourceTag);
    }

    public static function emergency($error, $context = [], $sourceTag = '')
    {
        return self::exception('emergency', $error, $context, $sourceTag);
    }

    private static function exception($type, $error, $context = [], $sourceTag)
    {
        $context['sourceTag'] = $sourceTag;
        //------------------------------------ DB query Log Include
        if(self::$dbQueryEnable){
            $context['dbQueries'] = DB::getQueryLog();
            self::$dbQueryEnable = false;
        }

        Log::$type($error, $context);
    }

    private static function log($type, $message, $context = [],$sourceTag = '' , $traceInclude = true)
    {
        $context['sourceTag'] = $sourceTag;

        //------------------------------------ File Tracing Include
        if($traceInclude){
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $context['file'] = $trace[1]['file'];
            $context['line'] = $trace[1]['line'];
        }

        //------------------------------------ DB query Log Include
        if(self::$dbQueryEnable){
            $context['dbQueries'] = DB::getQueryLog();
            self::$dbQueryEnable = false;
        }
        Log::$type($message, $context);
    }
}


