<?php
namespace Presto\Traits;

trait Singletonable
{
    private static $instance;

    public static function getInstance()
    {
        if(! self::$instance)
        {
            self::$instance = new static;
            self::$instance->init();
        }

        return self::$instance;
    }

    /**
     * 初期化用IF
     */
    protected function init() { }
}