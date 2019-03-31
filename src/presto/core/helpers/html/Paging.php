<?php
namespace Presto\Core\Helpers\Html;

use Presto\Core\Traits\Singletonable;

class Paging
{
    use Singletonable;

    const PAGINATOR_COUNT = 10;
    const LIMIT_COUNT = 20;

    // ページング取得
    public function paging(array $rows, int $page=1, int $limit=self::LIMIT_COUNT)
    {
        $count = count($rows);

        list($start, ) = $this->getStartEndRowNumber($count, $page, $limit);
        $target_rows = array_slice($rows, $start, $limit);

        return [$target_rows, $count];
    }

    // 表示開始と終了No
    public function getStartEndRowNumber(int $count=0, int $page=1, int $limit=self::LIMIT_COUNT)
    {
        $start = $limit * ($page - 1);
        $end = $limit * $page;

        $start = ($start <= 1) ? 0 : $start;
        $end = ($end > $count) ? $count : $end;

        return [$start, $end];
    }

    // 総ページ数の取得
    public function getTotalPageCount(int $count, int $limit=self::LIMIT_COUNT)
    {
        return ceil($count / $limit);
    }


    // -------------------------------------------------------
    // ページングヘルパー
    // -------------------------------------------------------
    // ページングヘルパー
    public function html(int $count, int $limit=self::LIMIT_COUNT)
    {
        $page = (int)input("page", 1);
        $base_url = $this->baseurl();

        list($start_i, $end_i) = $this->getStartEndRowNumber($count, $page);
        $total_page = $this->getTotalPageCount($count);
        list($start, $end) = $this->getStartEndPageNumber($total_page, $page);

        echo "<ul class='pagination justify-content-end'>";
        echo "<li class='page-item disabled'><span class='page-link'>Count: {$start_i}~{$end_i} / {$count}</span></li>";
        echo "<li class='page-item disabled'><span class='page-link'>Pages: {$page} / {$total_page}</span></li>";

        if($page == 1)
        {
            echo "<li class='page-item disabled'><span class='page-link'><span class='fa fa-angle-double-left'></span></span></li>";
            echo "<li class='page-item disabled'><span class='page-link'><span class='fa fa-angle-left'></span></span></li>";
        }
        else
        {
            $prev = $page - 1;
            echo "<li class='page-item'><a href='{$base_url}1' class='page-link'><span class='fa fa-angle-double-left'></span></a></li>";
            echo "<li class='page-item'><a href='{$base_url}{$prev}' class='page-link'><span class='fa fa-angle-left'></span></a></li>";
        }

        for($i=$start; $i<=$end; $i++)
        {
            if($i == $page)
            {
                echo "<li class='page-item active'><span class='page-link'>{$i}</span></li>";
                continue;
            }

            echo "<li class='page-item'><a href='{$base_url}{$i}' class='page-link'>{$i}</a></li>";
        }

        if($page == $end)
        {
            echo "<li class='page-item disabled'><span class='page-link'><span class='fa fa-angle-right'></span></span></li>";
            echo "<li class='page-item disabled'><span class='page-link'><span class='fa fa-angle-double-right'></span></span></li>";
        }
        else
        {
            $next = $page + 1;
            echo "<li class='page-item'><a href='{$base_url}{$next}'  class='page-link'><span class='fa fa-angle-right'></span></a></li>";
            echo "<li class='page-item'><a href='{$base_url}{$total_page}'  class='page-link'><span class='fa fa-angle-double-right'></span></a></li>";
        }

        echo "</ul>";
    }


    public function baseurl()
    {
        $base_url = $_SERVER["REQUEST_URI"];
        $base_url = preg_replace("/page=[0-9]*/", "", $base_url);
        $base_url .= preg_match("/\?/", $base_url) ? "&page=" : "?page=";
        $base_url = preg_replace("/&{2,}/", "&", $base_url);

        return $base_url;
    }

    public function getStartEndPageNumber(int $total_page, int $page=1)
    {
        $start = $page - ceil(self::PAGINATOR_COUNT / 2);
        $start = ($start <= 1) ? 1 : $start;

        $end = $start + self::PAGINATOR_COUNT - 1;
        $end = ($end >= $total_page) ? $total_page : $end;

        return [$start, $end];
    }
    // -------------------------------------------------------

}