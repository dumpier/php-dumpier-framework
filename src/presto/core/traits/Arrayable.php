<?php
namespace Presto\Core\Traits;

trait Arrayable
{
    public function __construct(array $array)
    {
        foreach ($array as $property=>$val)
        {
            if($val === NULL)
            {
                continue;
            }

            if(property_exists($this, $property))
            {
                $this->{$property} = $val;
            }
        }
    }

}