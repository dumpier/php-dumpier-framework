<?php
namespace Presto\Traits;

trait Towable
{
    public function toArray()
    {
        return (array)$this;
    }


    public function toJson()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }


    public function toString()
    {

    }

}