<?php

namespace Logger\builder;

use Exception;
use Logger\factory\ConsoleLogFactory;
use Logger\factory\FileLogFactory;
use Logger\factory\RabbitMQLogFactory;
use Logger\service\RabbitMQInstance;

// logger factory builder
class BuilderFactory
{
    const LOG_FACTORY_CONSOLE = 1;
    const LOG_FACTORY_FILE = 2;
    const LOG_FACTORY_RABBITMQ = 3;

    public $loggerTypes = [];

    protected $loggerFactorys = [];

    protected $rabbitMQ = '';
    protected $queueRequest = '';

    public function __construct($rabbitMQ = '', $queueRequest = '')
    {
        $this->rabbitMQ = $rabbitMQ;
        $this->queueRequest = $queueRequest;
    }

    public function setLoggerTypes(array $types)
    {
        $this->loggerTypes = $types;

        $this->initLoggerFactory();

        return $this;
    }

    public function addLoggerType(int $type)
    {
        $this->loggerTypes[] = $type;
        $this->loggerTypes = array_unique($this->loggerTypes);

        $this->initLoggerFactory();

        return $this;
    }

    protected function initLoggerFactory()
    {
        if (!$this->loggerTypes) {
            $this->loggerFactorys = [];
        } else {
            foreach ($this->loggerTypes as $type) {
                if ($type == self::LOG_FACTORY_CONSOLE && !isset($this->loggerFactorys[self::LOG_FACTORY_CONSOLE])) {
                    $this->loggerFactorys[self::LOG_FACTORY_CONSOLE] = new ConsoleLogFactory();
                } elseif ($type == self::LOG_FACTORY_FILE && !isset($this->loggerFactorys[self::LOG_FACTORY_FILE])) {
                    $this->loggerFactorys[self::LOG_FACTORY_FILE] = new FileLogFactory();
                } elseif ($type == self::LOG_FACTORY_RABBITMQ && !isset($this->loggerFactorys[self::LOG_FACTORY_RABBITMQ])) {
                    if ($this->rabbitMQ instanceof RabbitMQInstance) {
                        $this->loggerFactorys[self::LOG_FACTORY_RABBITMQ] = new RabbitMQLogFactory($this->rabbitMQ, $this->queueRequest);
                    } else {
                        throw new Exception('has not configed of mq instance');
                    }
                }
            }
        }
        return $this->loggerFactorys;
    }

    public function process()
    {
        if ($this->loggerFactorys) {
            foreach ($this->loggerFactorys as $logger) {
                $logger->process();
            }
        }
    }

    public function __call($name, $arguments)
    {
        if ($this->loggerFactorys) {
            foreach ($this->loggerFactorys as $logger) {
                if (method_exists($logger, $name)) {
                    $logger->$name(...$arguments);
                }
            }
        }
    }
}
