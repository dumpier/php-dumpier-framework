<?php
namespace Presto\Core\Helpers\Html;

use Presto\Core\Traits\Singletonable;

class SelectTag
{
    use Singletonable;

    public function render(string $tagname, array $rows, $default=0, string $title="選択", string $append="")
    {
        echo "<select name={$tagname} class=\"form-control\">";

        echo "<option value=0>-- {$title} --</option>";

        foreach ($rows as $key=>$val)
        {
            echo "<option value=\"{$key}\">{$val}{$append}</option>";
        }

        echo "</select>";
    }


}