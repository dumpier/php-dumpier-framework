<?php
namespace Presto\Core\Traits;

trait Arrayable
{
    public function __construct(array $statuses)
    {
        foreach ($statuses as $property=>$val)
        {
            if(property_exists($this, $property))
            {
                $this->{$property} = $val;
            }
        }
    }

}