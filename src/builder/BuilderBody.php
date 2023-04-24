<?php

namespace Logger\builder;

class BuilderBody
{
    protected $bodys = [];

    protected static $instance;
    protected function __construct()
    {
    }
    protected function __clone()
    {
    }
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addBody($type, ...$args)
    {
        $this->bodys[$type][] = $args;
    }

    public function getBodys($type)
    {
        return $this->bodys[$type] ?? [];
        // foreach ($this->bodys as $body) {
        //     yield $body;
        // }
    }
}
