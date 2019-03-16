<?php
namespace Presto\Core\Traits;

trait Instanceable
{
    public static function instance(...$parameters)
    {
        return new static(...$parameters);
    }
}