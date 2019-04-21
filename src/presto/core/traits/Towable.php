<?php
namespace Presto\Core\Traits;

/**
 * @property array $properties
 */
trait Towable
{
    public function toArray()
    {
        // TODO
        if( empty($this->properties) )
        {
            return (array)$this;
        }

        // TODO
        $array = [];
        foreach ($this->properties as $property)
        {
            $array[$property] = property_exists($this, $property) ? $this->{$property} : null;
        }

        return $array;
    }


    public function toJson()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

}