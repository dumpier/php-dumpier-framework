<?php
namespace Presto\Traits;

trait Accessible
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


}