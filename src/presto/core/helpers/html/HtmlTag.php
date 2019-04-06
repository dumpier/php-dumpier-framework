<?php
namespace Presto\Core\Helpers\Html;

use Presto\Core\Utilities\Arrayer;
use Presto\Core\Traits\Singletonable;

class HtmlTag
{
    use Singletonable;

    /**
     * XSS対応の表示
     * @param mixed $val
     */
    public function echo($val)
    {
        if(is_array($val))
        {
            $string = Arrayer::instance()->toTreeString($val);

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