<?php
namespace Presto\Traits;

trait Properties
{
    protected function accessor(string $property, $input=null)
    {
        if($input)
        {
            $this->{$property} = $input;
            return $this;
        }

        return $this->{$property};
    }


    public function get(string $property="")
    {
        return $this->{$property};
    }


    public function set(string $property, $input)
    {
        $this->{$property} = $input;
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