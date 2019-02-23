<?php
namespace Presto\Traits;

trait Instanceable
{
    public static function instance(...$parameters)
    {
        return new static(...$parameters);
    }
}