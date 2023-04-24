<?php

namespace Logger\factory;

use Logger\interfaces\LoggerInterface;

// log factory by console.
class ConsoleLogFactory extends LoggerFactory implements LoggerInterface
{
    public function requestHandle()
    {
        $content = "\r\n\r\n";
        foreach ($this->getRequestDatas() as $key => $value) {
            $content .= $key . ':' . $value . "\r\n";
        }
        
        $this->sendBodyArgs = [$content];
    }

    public function handle()
    {
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
            $this->sendBodyArgs = [$content];
        }
    }

    protected function sendBody()
    {
        echo $this->sendBodyArgs[0];
    }
}
