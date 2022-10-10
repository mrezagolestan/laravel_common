<?php

namespace Piod\LaravelCommon\Console;

use App\Piod\EventGet;
use Illuminate\Console\Command;
use Piod\LaravelCommon\Rabbit;
use Illuminate\Support\Facades\Log;

class IncomingEventFromRabbit extends Command
{

    private $waitSecond = 5;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incomingEventFromRabbit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $getEventsList = config('piod_common.events.get_events_list');
        Rabbit::consume(function($payload,$headers) use($getEventsList){
            $type = $headers['type'];

            if ( $headers['source'] == config('piod_common.events.source_name') ) {
                //return true;
            }
            $headers['payload'] = $payload;
            if(method_exists(EventGet::class,$type) && in_array($type,$getEventsList)){
                try{
                    EventGet::$type($headers['stored_at'],$headers['user_id'],$headers['vehicle_device_id'],$payload);
                    Log::info('Events:get:'.$type.':done',$headers);
                    return true;
                }catch(\Throwable $e){
                    Log::error($e);
                    return false;
                }
            }else{
                Log::info('Events:get:'.$type.':not_found',$headers);
                return true;
            }
        },config('piod_common.events.get_queue_name'));

    }
}
