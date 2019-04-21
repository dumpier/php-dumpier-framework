<?php
namespace Presto\Core\Utilities;

use Presto\Core\Traits\Instanceable;
use Presto\Core\Helpers\Html\PagerTag;
use Presto\Core\Traits\Accessible;

class Paginator
{
    use Instanceable;
    use Accessible;


    protected $limit = PagerTag::LIMIT_COUNT;
    protected $page = 0;
    protected $count = 0;

    /** @var Collection|array */
    protected $rows;


    public function __construct($rows)
    {
        $this->rows = $rows;
    }

    // 現在表示中のページ
    public function page(int $input=null)
    {
        return $this->accessor("page", $input);
    }

    // １ページの表示件数
    public function limit(int $input=null)
    {
        return $this->accessor("limit", $input);
    }

    public function count(int $input=null)
    {
        return $this->accessor("count", $input);
    }

    public function rows(array $input=null)
    {
        return $this->accessor("rows", $input);
    }


    public function all()
    {
        return $this->rows;
    }


    public function toArray()
    {
        if($this->rows instanceof Collection)
        {
            return $this->rows->toArray();
        }

        return $this->rows;
    }
}