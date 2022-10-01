<?php

namespace Piod\LaravelCommon;

use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire;


class Rabbit
{
    private static $waitSecond = 5;
    private static $rabbitPublishConnection;
    private static $rabbitConsumeConnection;
    private static $rabbitPublishChannel;
    private static $rabbitConsumeChannel;


    /**
     *
     * Convert an object to an array
     *
     * @param object $object The object to convert
     * @return      array
     *
     */
    public static function publish($persistent, $body, $exchange, $queue_name, $headers = [])
    {
        $tryCount = 0;
        while (true) {
            try {
                //Connect to RabbitMQ
                self::connect('publish');
                $tryCount = 0;
                $body = json_encode($body);
                //Make Channel
                if (!self::$rabbitPublishChannel) {
                    self::$rabbitPublishChannel = self::$rabbitPublishConnection->channel();
                }
                //Make & Send Message
                $msg = new AMQPMessage($body, [
                    'delivery_mode' => ($persistent ? AMQPMessage::DELIVERY_MODE_PERSISTENT : AMQPMessage::DELIVERY_MODE_NON_PERSISTENT)
                ]);
                if ($headers != []) {
                    $headers = new Wire\AMQPTable($headers);
                    $msg->set('application_headers', $headers);
                }
                return self::$rabbitPublishChannel->basic_publish($msg, $exchange, $queue_name);
            } catch (\Exception $e) {
                $tryCount++;
                self::disconnectLog($e, 'publish', $tryCount);
                self::cleanup_connection('publish');
                usleep((self::$waitSecond * 1000000));
            }
        }
        self::$rabbitPublishConnection->close();
    }

    public static function consume($callbackFunction,$queueName,$autoAck = false,$prefetchSize = 1)
    {
        $tryCount = 0;
        while (true) {
            try {
                //Connect to RabbitMQ
                self::connect('consume');
                $tryCount = 0;
                //Consume
                if (!self::$rabbitConsumeChannel) {
                    self::$rabbitConsumeChannel = self::$rabbitConsumeConnection->channel();
                }
                self::$rabbitConsumeChannel->basic_qos(null, $prefetchSize, null);
                self::$rabbitConsumeChannel->basic_consume($queueName, '', false, $autoAck, false, false, function ($msg) use ($callbackFunction, $autoAck) {

                    $body = json_decode($msg->body);
                    $headers = $msg->get_properties()['application_headers'] ?? [];
                    $callbackResponse = $callbackFunction($body, $headers);
                    //if need to be acked
                    if (!$autoAck && $callbackResponse) {
                        $msg->ack();
                    } else if (!$autoAck && !$callbackResponse) {
                        $msg->nack();
                    }
                });
                while (self::$rabbitConsumeChannel->is_open()) {
                    self::$rabbitConsumeChannel->wait();
                }
            } catch (\Exception $e) {
                dd($e);
                $tryCount++;
                self::disconnectLog($e, 'consume', $tryCount);
                self::cleanup_connection('consume');
                usleep((self::$waitSecond * 1000000));
            }
        }
        self::$rabbitConsumeConnection->close();
    }

    //------------------------ Basic Methods
    public function __destruct()
    {
        self::shutdown();
    }

    private static function connect($type)
    {
        if ($type == 'publish') {
            $typeConnection = 'rabbitPublishConnection';
        } else {
            $typeConnection = 'rabbitConsumeConnection';
        }
        if (!self::$$typeConnection) {
            $host = config('piod_common.rabbitmq.host');
            $port = config('piod_common.rabbitmq.port');
            $user = config('piod_common.rabbitmq.user');
            $password = config('piod_common.rabbitmq.password');
            $provider = config('piod_common.rabbitmq.vhost');

            // If you want a better load-balancing, you cann reshuffle the list.
            self::$$typeConnection = AMQPStreamConnection::create_connection([
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
    }

    private static function cleanup_connection($type)
    {
        if ($type == 'publish') {
            $typeConnection = 'rabbitPublishConnection';
            $typeChannel = 'rabbitPublishChannel';
        } else {
            $typeConnection = 'rabbitConsumeConnection';
            $typeChannel = 'rabbitConsumeChannel';
        }
        // Connection might already be closed.
        // Ignoring exceptions.
        try {
            if (self::$$typeConnection !== null) {
                self::$$typeConnection->close();
                self::$$typeConnection = null;
                self::$$typeChannel = null;
            }
        } catch (\ErrorException $e) {
            Log::warning($e);
        }
    }

    private static function shutdown()
    {
        echo 'RabbitMQ:connection shutdown
';
        if (self::$rabbitPublishConnection) {
            self::$rabbitPublishConnection->close();
        }

        if (self::$rabbitConsumeConnection) {
            self::$rabbitConsumeConnection->close();
        }
    }

    private static function disconnectLog($error, $type, $tryTime)
    {
        $message = 'RabbitMQ:' . $type . 'connection: disconnected';

        echo $message . ':reconnect try ' . $tryTime . '
';

        if (!is_local()) {
            Log::critical($error, [
                'message' => $message,
                'tryTime' => $tryTime,
            ]);
        }
    }

}
