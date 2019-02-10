<?php
namespace Presto\Helpers;

use Presto\Traits\Singletonable;

class Breadcrumb
{
    use Singletonable;

    protected $breadcrumbs = [];

    public function adds(array $breadcrumbs=[])
    {
        foreach ($breadcrumbs as $bread)
        {
            if(empty($bread["title"]))
            {
                throw new \Exception("Breadcrumb設定エラー");
            }

            $url = empty($bread["url"]) ? "" : $bread["url"];
            $this->breadcrumbs[] = [
                "title"=>$bread["title"],
                "url"=>$url,
            ];
        }

        return $this;
    }

    public function add(string $title, string $url="")
    {
        $this->breadcrumbs[] = [
            "title"=>$title,
            "url"=>$url,
        ];

        return $this;
    }

    public function all()
    {
        return $this->breadcrumbs;
    }

    public function show()
    {
        echo "<ol class='breadcrumb'>";

        foreach ($this->breadcrumbs as $bread)
        {
            echo "<li class='breadcrumb-item'><a href='{$bread["url"]}'>Home</a></li>";
        }

        echo "</ol>";
    }
}