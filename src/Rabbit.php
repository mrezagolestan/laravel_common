<?php

namespace Piod\LaravelCommon;

use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire;


class Rabbit
{
    private static $waitSecond = 5;
    private static $rabbitConnection;


    public static function publish($body, $exchange, $queue_name, $headers = [])
    {
        $try = true;
        while ($try) {
            try {
                //Connect to RabbitMQ
                self::connect();

                //Publish
                $body = json_encode($body);
                //Make Channel
                $channel = self::$rabbitConnection->channel();
                //Make & Send Message
                $msg = new AMQPMessage($body, [
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
                ]);
                if ($headers != []) {
                    $headers = new Wire\AMQPTable($headers);
                    $msg->set('application_headers', $headers);
                }
                return $channel->basic_publish($body, $exchange, $queue_name, $headers);
            } catch (\Exception $e) {
                if (is_local()) {
                    dd($e);
                } else {
                    Log::critical($e, [
                        'message' => 'RabbitListener:RabbitMQ connection disconnected, try to reconnect ...'
                    ]);
                    $try = true;
                    self::cleanup_connection();
                    usleep((self::$waitSecond * 1000000));
                }
            }
        }
        self::$rabbitConnection->close();
    }

    public static function consume($queueName, $callbackFunction)
    {
        $try = true;
        while ($try) {
            try {
                //Connect to RabbitMQ
                self::connect();

                //Consume
                $channel = self::$rabbitConnection->channel();
                $channel->basic_qos(null, 1, null);
                $channel->basic_consume($queueName, '', false, false, false, false, function ($msg) use ($callbackFunction) {
                    $callbackFunction($msg);
                });
                while ($channel->is_open()) {
                    $channel->wait();
                }
            } catch (\Exception $e) {
                if (is_local()) {
                    dd($e);
                } else {
                    Log::critical($e, [
                        'message' => 'RabbitListener:RabbitMQ connection disconnected, try to reconnect ...'
                    ]);
                    $try = true;
                    self::cleanup_connection();
                    usleep((self::$waitSecond * 1000000));
                }
            }
        }
        self::$rabbitConnection->close();
    }

    //------------------------ Basic Methods
    private static function connect()
    {
        if (!self::$rabbitConnection) {
            $host = config('piod.rabbitmq.host');
            $port = config('piod.rabbitmq.port');
            $user = config('piod.rabbitmq.user');
            $password = config('piod.rabbitmq.password');
            $provider = config('piod.rabbitmq.vhost');

            // If you want a better load-balancing, you cann reshuffle the list.
            self::$rabbitConnection = AMQPStreamConnection::create_connection([
                    ['host' => $host, 'port' => $port, 'user' => $user, 'password' => $password, 'vhost' => $provider],
                ]
                , [
                    'insist' => false,
                    'login_method' => 'AMQPLAIN',
                    'login_response' => null,
                    'locale' => 'en_US',
                    'connection_timeout' => 3.0,
                    'read_write_timeout' => 3.0,
                    'context' => null,
                    'keepalive' => false,
                    'heartbeat' => 0
                ]
            );
        }
        return self::$rabbitConnection;
    }

    private static function cleanup_connection()
    {
        // Connection might already be closed.
        // Ignoring exceptions.
        try {
            if (self::$rabbitConnection !== null) {
                self::$rabbitConnection->close();
            }
        } catch (\ErrorException $e) {
            Log::warning($e);
        }
    }


}
