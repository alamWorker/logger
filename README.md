
#
## Record log to file.
    use Logger\factory\FileLogFactory;
    $logger = new FileLogFactory();
    $logger->setTag('test');
    $logger->setDatas([
        'test1' => 'test2',
        'test3' => 'test4',
    ]);
    $logger->process();

    // request file
    /log/request/2023/04/21/16/27.log
    batch_id:15636c24
    time:1682065652346337
    request_method:GET
    http_host:127.0.0.1:8081
    request_uri:/index.php?test1=test1
    datetime:2023-04-21 16:27:32

    // data file
    /log/15636c24.log
    tag:TEST
    datetime:2023-04-21 16:27:32
    test1:test2
    test3:test4
#

## Record log to console.
    use Logger\factory\ConsoleLogFactory;
    $logger = new ConsoleLogFactory();
    $logger->setTag('test');
    $logger->setDatas([
        'test1' => 'test2',
        'test3' => 'test4',
    ]);
    $logger->process();

    // send content
    batch_id:0f0d7cf5
    time:1682065530596578
    request_method:GET
    http_host:127.0.0.1:8081
    request_uri:/index.php?test1=test1
    datetime:2023-04-21 16:25:30

    tag:TEST
    datetime:2023-04-21 16:25:30
    test1:test2
    test3:test4
#

## Record log to mq.
    use Logger\factory\RabbitMQLogFactory;
    use Logger\service\RabbitMQ;
    $mqHost = 'rabbitmq';
    // $mqPort = '5672';
    // $mqUser = 'guest';
    // $mqPwd = 'guest';
    $instance = RabbitMQ::getInstance();
    $instance->initAmqp($mqHost);

    $logger = new RabbitMQLogFactory($instance, 'request_queue_name');
    $logger->setTag('tag_name');
    $logger->setQueueData('data_queue_name');
    $logger->setDatas([
        'test1' => 'test2',
    ]);
    $logger->process();
#

## How to record log to more channel?
use Logger\builder\Builder;
use Logger\service\RabbitMQInstance;
$instance = RabbitMQInstance::getInstance();
$instance->initAmqp('rabbitmq');

$logger = new Builder($instance, 'queue_request_name');
// $logger->addLoggerType($logger::LOG_FACTORY_CONSOLE);
// $logger->addLoggerType($logger::LOG_FACTORY_FILE);
// $logger->addLoggerType($logger::LOG_FACTORY_RABBITMQ);
$logger->setLoggerTypes([$logger::LOG_FACTORY_CONSOLE, $logger::LOG_FACTORY_FILE, $logger::LOG_FACTORY_RABBITMQ]);

$logger->setTag('tag_name');
$logger->setQueueData('queue_data_name');

$logger->setDatas([
    'test1' => 'test2',
    'test3' => 'test4',
]);
$logger->process();
#

## How to modify instance for MQ?
    use Logger\service\RabbitMQ;
    $mqHost = 'rabbitmq';
    // $mqPort = '5672';
    // $mqUser = 'guest';
    // $mqPwd = 'guest';
    $instance = RabbitMQ::getInstance();
    $instance->initAmqp($mqHost);

    $logger->setInstance($instance);
#

## How to modify queue name for MQ log recording?
    // set queue name for request log
    new RabbitMQLogFactory($instance, 'request_queue_name');
    // set queue name for data log
    $logger->setQueueData('data_queue_name');
#

## How to add request data?
    // set request data, just append
    $logger->setRequestData('key', 'value');
    // set request data list, just append
    $logger->setRequestDatas(['key', 'value']);
#

## How to add data?
    // set data, just append
    $logger->setData('key', 'value');
    // set data list, just append
    $logger->setDatas(['key', 'value'], true);
    // set data list, complete replacement
    $logger->setDatas(['key', 'value']);
#

## How to async record?
    // notice, must seted after add logger type
    $logger->setAsync(true);
#

## Realizing true asynchrony
1、Define a class extend of \Logger\builder\BuilderBody，rewrite function which named addBody and getBodys;

2、write async program:
    $bodys = $this->builderBody->getBodys(static::class);
    foreach ($bodys as $body) {
        $logFactory->sendBodyArgs = $body;
        $logFactory->sendBody();
    }
#