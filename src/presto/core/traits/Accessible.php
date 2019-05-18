<?php
namespace Presto\Core\Traits;

trait Accessible
{
    protected function accessor(string $property, $input=null)
    {
        if($input===null)
        {
            return $this->get($property);
        }

        return $this->set($property, $input);
    }


    public function get(string $property="")
    {
        return $this->{$property};
    }


    public function set(string $property, $input)
    {
        $this->{$property} = $input;
        return $this;
    }


}