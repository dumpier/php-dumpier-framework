<?php
namespace Presto;

class Condition
{
    /** @var array */
    protected $condition;


    public function __construct(array $condition)
    {
        $this->condition = $condition;
    }
}