<?php
namespace Presto\Core\Traits\Bases;

/**
 * @property integer $position
 * @property array $rows
 *
 *
 */
trait IteratorTrait
{
    private $position = 0;

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->rows[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->rows[$this->position]);
    }
}