<?php
namespace Presto;

use Presto\Traits\Singletonable;

class Breadcrumb
{
    use Singletonable;

    protected $breadcrumb = [];

    public function all()
    {
        return $this->breadcrumb;
    }

    public function adds(array $rows=[])
    {
        foreach ($rows as $row)
        {
            if(empty($row["name"]))
            {
                throw new \Exception("Breadcrumb設定エラー");
            }

            $url = empty($row["url"]) ? "" : $row["url"];
            $this->breadcrumb[] = [
                "name"=>$row["name"],
                "url"=>$url,
            ];
        }

        return $this;
    }


    public function add(string $title, string $url="")
    {
        $this->breadcrumb[] = [
            "name"=>$title,
            "url"=>$url,
        ];

        return $this;
    }


    public function show()
    {
        echo "<ol class='breadcrumb'>";

        foreach ($this->breadcrumb as $bread)
        {
            echo "<li class='breadcrumb-item'><a href='{$bread["url"]}'>{$bread["name"]}</a></li>";
        }

        echo "</ol>";
    }
}