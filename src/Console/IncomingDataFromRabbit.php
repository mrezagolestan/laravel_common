<?php

namespace Piod\LaravelCommon\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Piod\LaravelCommon\Rabbit;
use App\Piod\Process;

class IncomingDataFromRabbit extends Command
{

    private $waitSecond = 5;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incomingDataFromRabbit';

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

        if(!config('piod_common.process.enable')){
            Log::critical('Data Process is Disabled');
            return 0;
        }

        Rabbit::consume(function($body,$headers){
            Process::handle($body,$headers);
        },config('piod_common.process.queue_name'));

        return 0;
    }


}
