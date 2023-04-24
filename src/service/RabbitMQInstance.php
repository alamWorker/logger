<?php

namespace Logger\service;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQInstance
{
    protected static $host;
    protected static $port;
    protected static $user;
    protected static $pwd;

    protected static $stream;
    protected static $channel;
    protected static $msg;

    private static $instance;
    private function __construct()
    {
    }
    private function __clone()
    {
    }
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function initAmqp($host, $port = '5672', $user = 'guest', $pwd = 'guest')
    {
        self::$stream = new AMQPStreamConnection($host, $port, $user, $pwd);
        self::$channel = self::$stream->channel();
        self::$msg = new AMQPMessage('', ['delivery_mode' => 1]);
        return self::$instance;
    }

    public static function setAmqp($channel, $msg)
    {
        self::$channel = $channel;
        self::$msg = $msg;
        return self::$instance;
    }

    public static function getChannel()
    {
        return self::$channel;
    }

    public static function getMsg()
    {
        return self::$msg;
    }

    public static function sendMsg(string $body, string $queue)
    {
        if (!self::$channel || !self::$msg) {
            throw new Exception('There has not setted for mq.');
        }
        self::initQueue($queue);
        self::$channel->basic_publish(self::$msg->setBody($body), '', $queue);
        return true;
    }

    public static function initQueue(string $queue)
    {
        static $list;
        if (empty($list[$queue])) {
            $list[$queue] = 1;
            self::$channel->queue_declare($queue, false, false, false, false);
        }
    }

    public function __destruct()
    {
        self::$channel->close();
    }
}
