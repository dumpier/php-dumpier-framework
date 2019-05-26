<?php
namespace Presto\Core\Helpers\Html;

use Presto\Core\Traits\Accessible;
use Presto\Core\Utilities\Pager;
use Presto\Core\Traits\Instanceable;

class PagerTag extends BaseTag
{
    use Instanceable;
    use Accessible;

    protected $css = "";
    protected $count = 0;
    protected $page = 1;
    protected $limit = Pager::LIMIT_COUNT;

    /** @var Pager */
    protected $pager;


    public function __construct()
    {
        $this->pager = Pager::instance(); // TODO 呼ばなくて済むように直す
        $this->page = $this->pager->page(); // TODO 呼ばなくて済むように直す
    }


    public function css(string $value=null)
    {
        return $this->accessor("css", $value);
    }

    public function count(int $value=null)
    {
        $this->pager->count($value); // TODO 呼ばなくて済むように直す
        return $this->accessor("count", $value);
    }

    public function page(int $value=null)
    {
        $this->pager->page($value); // TODO 呼ばなくて済むように直す
        return $this->accessor("page", $value);
    }

    public function limit(int $value=null)
    {
        $this->pager->limit($value); // TODO 呼ばなくて済むように直す
        return $this->accessor("limit", $value);
    }


    public function baseurl()
    {
        return $this->pager->baseurl();
    }



    // ページングヘルパー
    public function render(int $count=null)
    {
        $this->count = $count ? $count : $this->count;
        $this->pager->count($this->count); // TODO 呼ばなくて済むように直す

        $base_url = $this->pager->baseurl();

        list($start_i, $end_i) = $this->pager->getStartEndRowNumber($this->count, $this->page);
        $total_page = $this->pager->getTotalPageCount($this->count);
        list($start, $end) = $this->pager->getStartEndPageNumber($total_page, $this->page);

        echo "<ul class='pagination justify-content-end'>";
        echo "<li class='page-item disabled'><span class='page-link'>{$start_i}~{$end_i} / {$this->count}</span></li>";
        echo "<li class='page-item disabled'><span class='page-link'>Page: {$this->page} / {$total_page}</span></li>";

        if($this->page == 1)
        {
            echo "<li class='page-item disabled'><span class='page-link'><span class='fa fa-angle-double-left'></span></span></li>";
            echo "<li class='page-item disabled'><span class='page-link'><span class='fa fa-angle-left'></span></span></li>";
        }
        else
        {
            $prev = $this->page - 1;
            echo "<li class='page-item'><a href='{$base_url}1' class='page-link {$this->css}' p-target='{$this->target}'><span class='fa fa-angle-double-left'></span></a></li>";
            echo "<li class='page-item'><a href='{$base_url}{$prev}' class='page-link {$this->css}' p-target='{$this->target}'><span class='fa fa-angle-left'></span></a></li>";
        }

        for($i=$start; $i<=$end; $i++)
        {
            if($i == $this->page)
            {
                echo "<li class='page-item active'><span class='page-link {$this->css}' p-target='{$this->target}'>{$i}</span></li>";
                continue;
            }

            echo "<li class='page-item'><a href='{$base_url}{$i}' class='page-link {$this->css}' p-target='{$this->target}'>{$i}</a></li>";
        }

        if($this->page == $end)
        {
            echo "<li class='page-item disabled'><span class='page-link'><span class='fa fa-angle-right'></span></span></li>";
            echo "<li class='page-item disabled'><span class='page-link'><span class='fa fa-angle-double-right'></span></span></li>";
        }
        else
        {
            $next = $this->page + 1;
            echo "<li class='page-item'><a href='{$base_url}{$next}'  class='page-link {$this->css}' p-target='{$this->target}'><span class='fa fa-angle-right'></span></a></li>";
            echo "<li class='page-item'><a href='{$base_url}{$total_page}'  class='page-link {$this->css}' p-target='{$this->target}'><span class='fa fa-angle-double-right'></span></a></li>";
        }

        echo "</ul>";
    }

}