<?php
namespace Presto\Core\Utilities;

use Presto\Core\Traits\Instanceable;
use Presto\Core\Traits\Accessible;
use Presto\Core\Request;

class Pager
{
    use Instanceable;
    use Accessible;

    const PAGER_COUNT = 10;
    const LIMIT_COUNT = 20;

    protected $count = 0;
    protected $page = 1;
    protected $limit = self::LIMIT_COUNT;


    public function __construct(int $page=1)
    {
        $this->page = (int)Request::instance()->input("page", 1);
        $this->page = empty($this->page) ? 1: $this->page;
    }

    public function count(int $value=null) { return $this->accessor("count", $value); }
    public function page(int $value=null) { return $this->accessor("page", $value); }
    public function limit(int $value=null) { return $this->accessor("limit", $value); }

    // ページング取得
    public function paging(array $rows)
    {
        $count = count($rows);

        list($start, ) = $this->getStartEndRowNumber();
        $target_rows = array_slice($rows, $start);

        return [$target_rows, $count];
    }

    /**
     * 表示開始と終了行番号
     */
    public function getStartEndRowNumber()
    {
        $start = $this->limit * ($this->page - 1);
        $end = $this->limit * $this->page;

        $start = ($start <= 1) ? 0 : $start;
        $end = ($end > $this->count) ? $this->count : $end;

        return [$start, $end];
    }

    /**
     * 総ページ数の取得
     */
    public function getTotalPageCount()
    {
        return ceil($this->count / $this->limit);
    }


    /**
     * 表示開始と終了ページ番号
     */
    public function getStartEndPageNumber(int $total_page)
    {
        $start = $this->page - ceil(self::PAGER_COUNT / 2);
        $start = ($start <= 1) ? 1 : $start;

        $end = $start + self::PAGER_COUNT - 1;
        $end = ($end >= $total_page) ? $total_page : $end;

        return [$start, $end];
    }


    public function baseurl()
    {
        $base_url = $_SERVER["REQUEST_URI"];
        $base_url = preg_replace("/page=[0-9]*/", "", $base_url);
        $base_url .= preg_match("/\?/", $base_url) ? "&page=" : "?page=";
        $base_url = preg_replace("/&{2,}/", "&", $base_url);

        return $base_url;
    }

}