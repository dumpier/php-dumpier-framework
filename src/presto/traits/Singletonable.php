<?php
namespace Presto\Traits;

trait Singletonable
{
    private static $instance;

    public static function instance(...$parameters)
    {
        if(! self::$instance)
        {
            self::$instance = new static(...$parameters);
            self::$instance->init();
        }

        return self::$instance;
    }


    /**
     * 初期化用IF
     */
    protected function initialize() { }
}