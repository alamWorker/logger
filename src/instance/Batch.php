<?php

namespace Logger\instance;

/**
 * 批次
 */
class Batch
{
    private static $batchId = '';

    private static $instance = null;
    private function __construct()
    {
    }
    private function __clone()
    {
    }
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param string $prefix
     */
    public function flushBatchId(string $prefix = '')
    {
        self::buildBatchIdByHash($prefix);
        return $this;
    }
    /**
     * @param string $prefix
     */
    private function buildBatchIdByHash(string $prefix = '')
    {
        $content = $prefix . randomString() . microtime();
        $content = hash('md5', $content);
        $content = mb_substr($content, 0, 8);

        self::$batchId = $content;
    }
    public function getBatchId()
    {
        return self::$batchId;
    }
}
