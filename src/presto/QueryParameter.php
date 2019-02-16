<?php
namespace Presto;

use Presto\Traits\Properties;

class QueryParameter
{
    use Properties;

    protected $page = 1;
    protected $offset = 0;
    protected $limit = Paging::LIMIT_COUNT;
    protected $order = [];
    protected $group = [];

    /** @var Condition */
    protected $condition;


    public function page(int $input=0)
    {
        return $this->accessor("page", $input);
    }

    public function offset(int $input=0)
    {
        return $this->accessor("offset", $input);
    }

    public function limit(int $input=0)
    {
        return $this->accessor("limit", $input);
    }

    public function order(array $input=[])
    {
        return $this->accessor("order", $input);
    }

    public function group(array $input=[])
    {
        return $this->accessor("group", $input);
    }

    public function condition(array $input=[])
    {
        return $this->accessor("condition", $input);
    }

}