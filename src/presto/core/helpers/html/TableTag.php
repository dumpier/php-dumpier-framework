<?php
namespace Presto\Core\Helpers\Html;

use Presto\Core\Traits\Singletonable;

class TableTag
{
    use Singletonable;

    protected $height;
    protected $header = [];
    protected $fields = [];
    protected $links = [];


    public function reset()
    {
        $this->header = [];
        $this->fields = [];
        $this->height = 0;
        $this->links = [];
    }

    /**
     * ヘッダーの指定
     * @param array $header
     * @return \Presto\Core\Helpers\Html\TableTag
     */
    public function header(array $header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * 出力項目の指定
     * @param array $fields
     * @return \Presto\Core\Helpers\Html\TableTag
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }


    /**
     * リンクの指定
     * @param string $key
     * @param string $url
     * @param string $prefix
     * @param string $attributes
     * @return \Presto\Core\Helpers\Html\TableTag
     */
    public function link(string $key, string $url, string $prefix, string $attributes)
    {
        $this->links[] = ["url"=>$url, "key"=>$key, "prefix"=>$prefix, "attributes"=>$attributes];
        return $this;
    }


    /**
     * 表示高さの指定
     * @param int $height
     * @return \Presto\Core\Helpers\Html\TableTag
     */
    public function height(int $height)
    {
        $this->height = $height;
        return $this;
    }


    /**
     * データ一覧の表示
     * @param array $rows
     * @param bool $is_reset パラメータをリセットするか
     * @return void|\Presto\Core\Helpers\Html\TableTag
     */
    public function render(array $rows, bool $is_reset=false)
    {
        if(empty($rows))
        {
            echo "データがない";
            return ;
        }

        // ヘッダー
        $this->header = empty($this->header) ? array_keys($rows[0]) : $this->header;

        // 出力項目
        $this->fields = empty($this->fields) ? $this->header : $this->fields;

        // 高さ
        $style = empty($this->height) ? "" : "height:{$this->height}px;";

        echo "<div class='table-responsive' style='{$style}'>";
        echo "<table class='table table-counter table-hover table-striped'>";

        // ヘッダー
        echo "<thead>";
        echo "<tr>";
        foreach ($this->fields as $field)
        {
            echo "<th>{$field}</th>";
        }
        echo "</tr>";
        echo "</thead>";

        $this->rows($rows);

        echo "</table>";

        echo "</div>";

        if ($is_reset)
        {
            // パラメータをリセットする
            $this->reset();
        }

        return $this;
    }

    private function rows(array $rows)
    {
        echo "</tbody>";

        foreach ($rows as $row)
        {
            $this->row($row);
        }

        echo "</tbody>";
    }

    private function row(array $row)
    {
        echo "<tr>";
        foreach ($this->fields as $field)
        {
            echo "<td>";

            if($field == arrayer()->get($this->links, "links.key"))
            {
                $url = arrayer()->get($this->links, "links.url") . $row[$field];
                $prefix = arrayer()->get($this->links, "links.prefix");
                $attributes = arrayer()->get($this->links, "links.attributes");

                echo "<a href='{$url}' {$attributes}>{$prefix}";
                html()->echo($row[$field]);
                echo "</a></td>";
            }
            else
            {
                html()->echo($row[$field]);
            }
            echo "</td>";
        }

        echo "</tr>";
    }

}