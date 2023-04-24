<?php

require_once __DIR__ . '/vendor/autoload.php';

use Logger\factory\ConsoleLogFactory;
$logger = new ConsoleLogFactory();
$logger->setTag('test');
$logger->setDatas([
    'test1' => 'test2',
    'test3' => 'test4',
]);
$logger->process();

// use Logger\factory\FileLogFactory;
// $logger = new FileLogFactory();
// $logger->setTag('test');
// $logger->setDatas([
//     'test1' => 'test2',
//     'test3' => 'test4',
// ]);
// $logger->process();

// use Logger\factory\RabbitMQLogFactory;
// use Logger\service\RabbitMQ;
// $instance = RabbitMQ::getInstance();
// $instance->initAmqp('rabbitmq');

// $logger = new RabbitMQLogFactory($instance, 'queue_request_name');
// $logger->setTag('tag_name');
// $logger->setQueueData('queue_data_name');
// $logger->setDatas([
//     'test3' => 'test4',
// ]);
// $logger->process();

// use Logger\builder\BuilderFactory;
// use Logger\service\RabbitMQInstance;
// $instance = RabbitMQInstance::getInstance();
// $instance->initAmqp('rabbitmq');

// $logger = new BuilderFactory($instance, 'queue_request_name');
// $logger->setLoggerTypes([$logger::LOG_FACTORY_CONSOLE, $logger::LOG_FACTORY_FILE, $logger::LOG_FACTORY_RABBITMQ]);

// $logger->setTag('tag_name');
// $logger->setQueueData('queue_data_name');

// $logger->setDatas([
//     'test1' => 'test2',
//     'test3' => 'test4',
// ]);
// $logger->process();
// $logger->setDatas([
//     'test2' => 'test4',
//     'test3' => 'test5',
// ]);
// $logger->process();