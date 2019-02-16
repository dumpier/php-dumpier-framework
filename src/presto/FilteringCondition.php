<?php
namespace Presto;

class FilteringCondition
{
    /** @var array */
    protected $condition;


    public function __construct(array $condition)
    {
        $this->condition = $condition;
    }
}