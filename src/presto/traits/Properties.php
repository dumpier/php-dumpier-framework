<?php
namespace Presto\Traits;

trait Properties
{
    public function get(string $name="")
    {

    }


    public function set(string $name, $value)
    {

    }


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