<?php

namespace Logger\factory;

use Logger\interfaces\LoggerInterface;

// log factory by file.
class FileLogFactory extends LoggerFactory implements LoggerInterface
{
    public function requestHandle()
    {
        $this->setFilePath(LOGGER_DS . 'log/request' . LOGGER_DS . date('Y') . LOGGER_DS . date('m') . LOGGER_DS . date('d') . LOGGER_DS . date('H'));
        $filePath = $this->getFilePath() . LOGGER_DS . date('i') . '.log';
        $content = "\r\n";
        foreach ($this->getRequestDatas() as $key => $value) {
            $content .= $key . ':' . $value . "\r\n";
        }
        $this->sendBodyArgs = [$filePath, $content, FILE_APPEND];
    }

    public function handle(array $data = [])
    {
        $this->setFilePath(LOGGER_DS . 'log');
        $filePath = $this->getFilePath() . $this->getFileName();

        if ($this->getDatas()) {
            $content = "\r\n";
            if ($this->getTag()) {
                $content .= 'tag:' . $this->getTag() . "\r\n";
            }
            $content .= 'time:' . time() . substr(microtime(), 2, 6) . "\r\n";
            $content .= 'datetime:' . date('Y-m-d H:i:s') . "\r\n";
            $data = $this->getDatas();
            foreach ($data as $kay => $value) {
                $content .= "{$kay}:{$value}\r\n";
            }
            
            $this->sendBodyArgs = [$filePath, $content, FILE_APPEND];
        }
    }

    protected function sendBody()
    {
        file_put_contents(...$this->sendBodyArgs) or die('log writed error, check or die.[file]');
    }
}
