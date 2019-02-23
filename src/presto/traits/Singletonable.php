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
        }

        return self::$instance;
    }

}