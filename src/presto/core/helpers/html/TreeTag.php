<?php
namespace Presto\Core\Helpers\Html;

use Presto\Core\Traits\Singletonable;

class TreeTag extends BaseTag
{
    use Singletonable;

    public function render(array $array, int $recursion=0)
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

}