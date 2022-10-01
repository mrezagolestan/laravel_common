<?php

namespace Piod\LaravelCommon\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class healthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'healthCheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check out service health';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $resources = config('piod_common.resource_list') ?? [];
        foreach($resources as $resource){
            $resource = explode(':',$resource);
            $resourceType = $resource[0];
            $resourceConnection = $resource[1] ?? '-';
            switch($resourceType){
                case 'db':
                    $result = $this->checkDatabase($resourceConnection);
                    break;

                case 'redis':
                    $result = $this->checkRedis();
                    break;

                case 'rabbitmq':
                    $result = $this->checkRabbit();
                    break;

                case 'sentry':
                    $result = $this->checkSentry();
                    break;
            }
            $message = $result['success'] ? '<fg=green>up</>' : '<fg=red>down</>';
            $this->line( $resourceType . ':' . $resourceConnection . ':' . $message);
            if (!$result['success']){
                $this->line("<fg=yellow>warning : </> <fg=red>{$result['message']}</>");
            }
        }
        return 1;
    }

    private function checkRedis()
    {
        try {
            $redis = Redis::connection();
            return[
                'success'=>$redis->ping(),
                'message'=>NULL
            ];
        } catch (\Exception $e) {
            return [
                'success'=>false,
                'message'=>$e->getMessage()
            ];
        }
    }

    private function checkDatabase($connection){

        try {
            DB::connection($connection)->getPdo();

            return [
                'success' => true,
                'message' => NULL,
            ];

        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }


    private function checkSentry(){

        try {
            Artisan::call('sentry:test');
            return [
                'success' => true,
                'message' => NULL,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

    }


    private function checkRabbit(){

        $host = config('piod_common.rabbitmq.host');
        $port = config('piod_common.rabbitmq.port');
        $user = config('piod_common.rabbitmq.user');
        $password = config('piod_common.rabbitmq.password');
        $vhost = config('piod_common.rabbitmq.vhost','/provider');

        try {
             new AMQPStreamConnection($host, $port, $user, $password,$vhost);
            return [
                'success'=>true,
                'message'=>NULL
            ];
        }
        catch (\Exception $e){
            return [
                'success'=>false,
                'message'=>$e->getMessage()
            ];
        }

    }
}
