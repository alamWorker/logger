<?php

namespace Logger\factory;

use DateTime;
use Logger\builder\BuilderBody;
use Logger\instance\Batch;
use Logger\interfaces\LoggerInterface;

// log factory by RabbimtMQ
class LoggerFactory implements LoggerInterface
{
    const Console       = 'Console';
    const File          = 'File';
    const RabbitMQ      = 'RabbitMQ';

    // seted
    protected $async                = false;    // async send
    protected $multiChannel         = false;    // multi channel
    protected $begin                = false;    // start marking
    protected $tag                  = '';       // start marking
    protected $builderBody          = '';       // body builder

    // common param
    protected $requestData          = []; // meta data, set for once request
    protected $data                 = []; // data, set for single row
    protected $allData              = []; // all data, set for single row
    protected $batchId              = ''; // batch id, builded by once require or period
    protected $sendBodyArgs         = []; // arguments for function send

    // only setted for File factory
    protected $rootPath             = ''; // root path
    protected $filePath             = ''; // save path
    protected $fileName             = ''; // saved file name
    // only setted for MQ factory
    protected $queueName            = ''; // send to queue

    public function __construct()
    {
        $this->builderBody = BuilderBody::getInstance();

        $this->setRootPath(defined('ROOT_PATH') ? LOGGER_ROOT_PATH : (__DIR__ . LOGGER_DS . '..' . LOGGER_DS . '..'));

        $this->initRequestData();
    }

    protected function initRequestData()
    {
        $this->setRequestData('batch_id', $this->setBatchId()->getBatchid());      // 批次ID
        $this->setRequestData('time', time() . substr(microtime(), 2, 6));         // 时间
        $this->setRequestData('datetime', date('Y-m-d H:i:s'));                    // 日期格式
        $this->setRequestData('request_method', $_SERVER['REQUEST_METHOD'] ?? ''); // 请求方法
        $this->setRequestData('http_host', $_SERVER['HTTP_HOST'] ?? '');           // 主机
        $this->setRequestData('request_uri', $_SERVER['REQUEST_URI'] ?? '');       // 统一资源标识符
    }

    public function process()
    {
        $this->allData = array_merge($this->requestData, $this->data);

        $this->beforeHandle();
        $this->handle();
        $this->afterHandle();
    }

    // record request base data
    public function requestHandle()
    {
        ### Implement in subclasses
    }

    protected function beforeHandle()
    {
        static $recordRequest;
        if (!$recordRequest) {
            $recordRequest = true; // only once record request log.
            $this->requestHandle();
            $this->afterHandle();
        }
    }
    public function handle()
    {
        ### Implement in subclasses
    }
    protected function afterHandle()
    {
        // async send body
        if ($this->async) {
            $this->builderBody->addBody(static::class, ...$this->sendBodyArgs);
        } else {
            $this->sendBody();
        }
    }

    protected function sendBody()
    {
        ### Implement in subclasses
    }

    public function __destruct()
    {
        if ($this->async) {
            // send async body
            $bodys = $this->builderBody->getBodys(static::class);
            foreach ($bodys as $body) {
                $this->sendBodyArgs = $body;
                $this->sendBody();
            }
        }
    }

    public function setBuilderBody(BuilderBody $builderBody)
    {
        $this->builderBody = $builderBody;
        return $this;
    }

    public function setBegin()
    {
        $this->begin = true;
        return $this;
    }

    public function getBegin()
    {
        return $this->begin;
    }

    public function setAsync(bool $async = true)
    {
        $this->async = $async;
        return $this;
    }

    public function getAsync()
    {
        return $this->async;
    }

    public function setTag(string $tag)
    {
        $this->tag = strtoupper($tag);
        return $this;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setRequestData(string $key, $value)
    {
        $this->requestData[$key] = $value;
        return $this;
    }

    public function getRequestData(string $key)
    {
        if (!isset($this->requestData[$key])) {
            $this->setRequestData($key, '');
        }
        return $this->requestData[$key];
    }

    public function setRequestDatas(array $data = [])
    {
        $this->requestData = array_merge($this->requestData, $data);
        return $this;
    }

    public function getRequestDatas()
    {
        return $this->requestData;
    }

    public function setDatas(array $data = [], bool $append = false)
    {
        if ($append) {
            $this->data = array_merge($this->data, $data);
        } else {
            $this->data = $data;
        }
        return $this;
    }

    public function getDatas()
    {
        return $this->data;
    }

    public function getDatas2Json()
    {
        return json_encode($this->data, JSON_UNESCAPED_UNICODE);
    }

    public function setBatchId()
    {
        $this->batchId = Batch::getInstance()->flushBatchId()->getBatchId();
        return $this;
    }

    public function getBatchid()
    {
        return $this->batchId;
    }

    public function setRootPath(string $rootPath)
    {
        !@file_exists($rootPath) && !@mkdir($rootPath, 0777, true);
        $this->rootPath = $rootPath;
        return $this;
    }

    public function getRootPath()
    {
        return $this->rootPath;
    }

    public function setFilePath(string $filePath)
    {
        !@file_exists($this->getRootPath() . $filePath) && !@mkdir($this->getRootPath() . $filePath, 0777, true);
        $this->filePath = $filePath;
        return $this;
    }

    public function getFilePath()
    {
        return $this->rootPath . $this->filePath;
    }

    public function setFileName(string $fileName = '', string $suffix = '.log')
    {
        if (empty($fileName)) {
            $date = new DateTime();
            $fileName = $date->format('YmdHisu');
        }
        $this->fileName = $fileName . $suffix;
        return $this;
    }

    public function getFileName()
    {
        if (!$this->fileName) {
            $this->setFileName($this->getBatchId());
        }
        return LOGGER_DS . ltrim($this->fileName, LOGGER_DS);
    }

    public function getFilePathWithName()
    {
        return $this->getFilePath() . $this->getFileName();
    }

    public function setQueueName(string $name)
    {
        $this->queueName = $name;
        return $this;
    }

    public function getQueueName()
    {
        return $this->queueName;
    }
}
