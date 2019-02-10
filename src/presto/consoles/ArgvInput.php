<?php
namespace Presto\Consoles;

use Presto\Traits\Singletonable;

class ArgvInput
{
    use Singletonable;

    private $argv;

    public function argument(string $name, $value=null)
    {


    }


    public function value(string $name, $default=null)
    {
        $request = $_REQUEST;

        if( ! isset($request[$name]) )
        {
            return $default;
        }

        if(is_array($request[$name]))
        {
            return arr()->clean($request[$name]);
        }

        return empty($request[$name]) ? $default : $request[$name];
    }
}