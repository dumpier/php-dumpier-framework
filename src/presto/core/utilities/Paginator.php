<?php
namespace Presto\Core\Utilities;

use Presto\Core\Traits\Instanceable;
use Presto\Core\Helpers\Html\PagerTag;

class Paginator
{
    use Instanceable;

    protected $limit = PagerTag::LIMIT_COUNT;
    protected $page = 0;
    protected $total_count = 0;
    protected $rows = [];


    public function __construct(array $rows, int $total_count, int $page=1, int $limit=PagerTag::LIMIT_COUNT)
    {
        $this->rows = $rows;
        $this->total_count = $total_count;
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
            return $this->total_count;
        }

        $this->total_count = $input;
        return $this;
    }

    public function rows(array $input=null)
    {
        if(null === $input)
        {
            return $this->rows;
        }

        $this->rows = $input;
        return $this;
    }


    public function all()
    {
        return $this->rows;
    }
}