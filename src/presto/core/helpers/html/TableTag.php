<?php
namespace Presto\Core\Helpers\Html;

use Presto\Core\Utilities\Arrayer;
use Presto\Core\Traits\Instanceable;

class TableTag extends BaseTag
{
    use Instanceable;

    protected $header = [];
    protected $fields = [];
    protected $links = [];
    protected $height = 0;


    /**
     * ヘッダーの指定
     * @param array $value
     * @return \Presto\Core\Helpers\Html\TableTag
     */
    public function header(array $value)
    {
        $this->header = $value;
        return $this;
    }

    /**
     * 出力項目の指定
     * @param array $value
     * @return \Presto\Core\Helpers\Html\TableTag
     */
    public function fields(array $value)
    {
        $this->fields = $value;
        return $this;
    }


    /**
     * リンクの指定
     * @param string $key
     * @param string $url
     * @param string $attribute
     * @param array $refrences
     * @return \Presto\Core\Helpers\Html\TableTag
     */
    public function link(string $key, string $url="", string $attribute="", array $refrences=[])
    {
        $this->links[$key] = ["url"=>$url, "attribute"=>$attribute, "refrences"=>$refrences];
        return $this;
    }


    /**
     * データ一覧の表示
     * @param array $rows
     * @return void|\Presto\Core\Helpers\Html\TableTag
     */
    public function render(array $rows)
    {
        if(empty($rows)) {
            echo "データがない";
            return ;
        }

        $this->header = empty($this->header) ? array_keys($rows[0]) : $this->header;
        $this->fields = empty($this->fields) ? $this->header : $this->fields;
        $style = empty($this->height) ? "" : "height:{$this->height}px;";


        // ヘッダー
        $html_header = "";
        foreach ($this->fields as $field) {
            $html_header .= "<th>{$field}</th>";
        }
        $html_header = "<thead><tr>{$html_header}</tr></thead>";

        // 行の出力
        $html_body = $this->rows($rows);

        $html = "<table class='table table-counter table-hover table-striped'>{$html_header}{$html_body}</table>";

        if($this->height) {
            $html = "<div class='table-responsive' style='{$style}'>{$html}</div>";
        }

        echo $html;

        return $this;
    }

    private function rows(array $rows)
    {
        $html = "";

        foreach ($rows as $key=>$row) {
            $html .= $this->row($row, $key);
        }

        return "<tbody>{$html}</tbody>";
    }

    private function row(array $row, $key="")
    {
        $html = "";

        foreach ($this->fields as $field) {
            $element_id = "{$field}-{$key}";

            if(Arrayer::instance()->get($this->links, $field)) {
                $html_field = $this->makeLink($row, $field, $element_id);
            } else {
                $html_field = HtmlTag::instance()->html($row[$field]);
            }

            $html .= "<td id={$element_id}>{$html_field}</td>";
        }

        return "<tr>{$html}</tr>";
    }


    private function makeLink(array $row, $field, string $element_id)
    {
        $url = Arrayer::instance()->get($this->links, "{$field}.url");
        $url = empty($url) ? "javascript: void(0);" : $url . $row[$field];

        $attribute = Arrayer::instance()->get($this->links, "{$field}.attribute");

        // TODO
        $refrences = Arrayer::instance()->get($this->links, "{$field}.refrences");
        $refrence_json = json_encode($row);

        $html = HtmlTag::instance()->html($row[$field]);
        return "<a href=\"{$url}\" {$attribute} refrence='{$refrence_json}'>{$html}</a>";
    }

}