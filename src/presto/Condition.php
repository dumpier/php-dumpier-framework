<?php
namespace Presto;

class Condition
{
    /** @var array */
    protected $conditions;


    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }
}