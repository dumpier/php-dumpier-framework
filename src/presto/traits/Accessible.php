<?php
namespace Presto\Traits;

trait Accessible
{
    protected function accessor(string $property, $input=null)
    {
        if($input)
        {
            $this->set($property, $input);
            return $this;
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
    }


}