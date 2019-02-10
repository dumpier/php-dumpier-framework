<?php
namespace Presto;

use Presto\Helpers\Paging;

class Paginator
{
    protected $limit = Paging::LIMIT_COUNT;
    protected $page = 0;
    protected $count = 0;
    protected $rows = [];

    public function __construct(array $rows, int $count, int $page=1, int $limit=Paging::LIMIT_COUNT)
    {
        $this->rows = $rows;
        $this->count = $count;
        $this->page = $page;
        $this->limit = $limit;
    }

    // 現在表示中のページ
    public function page(int $input=null)
    {
        if(null === $input)
        {
            return $this->page;
        }

        $this->page = $input;
        return $this;
    }

    // １ページの表示件数
    public function limit(int $input=null)
    {
        if(null === $input)
        {
            return $this->limit;
        }

        $this->limit = $input;
        return $this;
    }

    public function count(int $input=null)
    {
        if(null === $input)
        {
            return $this->count;
        }

        $this->count = $count;
        return $this;
    }

    public function rows(array $input=null)
    {
        $this->rows = $input;
        return $this;
    }


    public function all()
    {
        return $this->rows;
    }
}