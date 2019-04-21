<?php
namespace Presto\Core\Traits;

trait Accessible
{
    protected function accessor(string $property, $input=null)
    {
        if($input)
        {
            return $this->set($property, $input);
        }

        return $this->get($property);
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