<?php
namespace Presto\Helpers\Html;

use Presto\Traits\Singletonable;

class HtmlTag
{
    use Singletonable;

    public function table(array $rows, array $header=[], array $parameters=[])
    {
        if(empty($rows))
        {
            echo "データがない";
            return ;
        }

        // ヘッダー
        $header = empty($header) ? array_keys($rows[0]) : $header;

        // 出力項目
        $fields = empty($parameters["fields"]) ? $header : $parameters["fields"];

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

                if($field == arrayer()->get($parameters, "links.key"))
                {
                    $url = arrayer()->get($parameters, "links.url") . $row[$field];
                    $prefix = arrayer()->get($parameters, "links.prefix");
                    $attributes = arrayer()->get($parameters, "links.attributes");

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