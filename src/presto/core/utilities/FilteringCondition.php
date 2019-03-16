<?php
namespace Presto\Core\Utilities;

class FilteringCondition
{
    /** @var array */
    protected $condition;


    public function __construct(array $condition)
    {
        $this->condition = $condition;
    }
}