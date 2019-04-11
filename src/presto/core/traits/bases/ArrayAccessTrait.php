<?php
namespace Presto\Core\Traits\Bases;

/**
 * @property array $rows
 */
trait ArrayAccessTrait
{
    public function offsetExists ( $offset )
    {
        return isset($this->rows[$offset]);
    }


    public function offsetGet ( $offset )
    {
        return isset($this->rows[$offset]) ? $this->rows[$offset] : null;
    }


    public function offsetSet ( $offset , $value )
    {
        if (is_null($offset))
        {
            $this->rows[] = $value;
        }
        else
        {
            $this->rows[$offset] = $value;
        }
    }


    public function offsetUnset ( $offset )
    {
        unset($this->rows[$offset]);
    }

}