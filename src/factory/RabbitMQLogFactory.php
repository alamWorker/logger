<?php

namespace Logger\factory;

use Logger\interfaces\LoggerInterface;
use Logger\service\RabbitMQInstance;

// log factory by RabbimtMQ.
class RabbitMQLogFactory extends LoggerFactory implements LoggerInterface
{
    protected $queueRequest;
    protected $queueData;

    protected $rabbitMQ;

    public function __construct(RabbitMQInstance $rabbitMQ, string $queueRequest = '')
    {
        $this->rabbitMQ = $rabbitMQ;
        $this->queueRequest = $queueRequest;

        parent::__construct();
    }

    public function requestHandle()
    {
        $this->queueRequest && $this->sendBodyArgs = [json_encode($this->getRequestDatas(), JSON_UNESCAPED_UNICODE), $this->queueRequest];
    }

    public function handle()
    {
        if ($this->getDatas() && $this->queueData) {
            $body = array_merge([
                'batch_id' => $this->getBatchid(),
                'tag' => $this->getTag(),
                'time' => time() . substr(microtime(), 2, 6),
                'datetime' => date('Y-m-d H:i:s'),
            ], $this->getDatas());

            $this->sendBodyArgs = [json_encode($body, JSON_UNESCAPED_UNICODE), $this->queueData];
        }
    }

    protected function sendBody()
    {
        $this->rabbitMQ->getInstance()->sendMsg(...$this->sendBodyArgs) or die('log writed error, check or die.[rabbitmq]');
    }

    public function setInatance(RabbitMQInstance $rabbitMQ)
    {
        $this->rabbitMQ = $rabbitMQ;
        return $this;
    }

    public function setQueueRequest(string $queue)
    {
        $this->queueRequest = $queue;
        return $this;
    }

    public function setQueueData(string $queue)
    {
        $this->queueData = $queue;
        return $this;
    }
}
