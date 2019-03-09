<?php
namespace Presto\Helpers\Html;

use Presto\Traits\Singletonable;

class HtmlTag
{
    use Singletonable;

    public function tree(array $array, int $recursion=0)
    {
        if(empty($array))
        {
            return "";
        }

        $string = "<ul>";

        foreach ($array as $key=>$val)
        {
            if(is_array($val))
            {
                $string .= "<li>{$key} : ";
                $string .= $this->toTreeString($val, $recursion+1);
                $string .= "</li>";
                continue;
            }

            $string .="<li>{$key} : {$val}</li>";
        }

        $string .= "</ul>";
        return $string;
    }


    public function table(array $rows, array $header=[], array $parameter=[])
    {
        if(empty($rows))
        {
            echo "データがない";
            return ;
        }

        // ヘッダー
        $header = empty($header) ? array_keys($rows[0]) : $header;

        // 出力項目
        $fields = empty($parameter["fields"]) ? $header : $parameter["fields"];

        echo "<div class='table-responsive'>";
        echo "<table class='table table-counter table-hover table-striped'>";
        echo "<thead>";
        echo "<tr>";
        foreach ($fields as $field)
        {
            echo "<th>{$field}</th>";
        }
        echo "</tr>";
        echo "</thead>";


        echo "</tbody>";
        foreach ($rows as $no=>$row)
        {
            echo "<tr>";
            foreach ($fields as $field)
            {
                echo "<td>";

                if($field == arrayer()->get($parameter, "links.key"))
                {
                    $url = arrayer()->get($parameter, "links.url") . $row[$field];
                    $prefix = arrayer()->get($parameter, "links.prefix");
                    $attributes = arrayer()->get($parameter, "links.attributes");

                    echo "<a href='{$url}' {$attributes}>{$prefix}";
                    $this->echo($row[$field]);
                    echo "</a></td>";
                }
                else
                {
                    $this->echo($row[$field]);
                }
                echo "</td>";
            }

            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";

        echo "</div>";
    }

    public function echo($val)
    {
        if(is_array($val))
        {
            $string = arrayer()->toTreeString($val);
            if(empty($string))
            {
                return ;
            }

            echo <<<EOF
<div style='position:relative;' onmouseover="$(this).children('div').show();" onmouseout="$(this).children('div').hide();">
  <a href='javascript:void(0);'>view</a>
  <div style='position:absolute; z-index:1001; top:0px; left:30px; background:#fff; padding:10px; border:solid 3px #ddd; display:none;'>{$string}</div>
</div>
EOF;
        }
        else
        {
            echo $val;
        }

    }

}