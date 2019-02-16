<?php
namespace Presto;

use Presto\Traits\Singletonable;

class Request
{
    use Singletonable;

    public function input(string $key, $default_value=null)
    {
        $result = isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default_value;

        return $result;
    }
}