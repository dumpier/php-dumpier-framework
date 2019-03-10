<?php
namespace Presto;

use Presto\Traits\Singletonable;

class Session
{
    use Singletonable;

    public function all()
    {
        return $_SESSION;
    }

    public function get(string $key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }


    public function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }


    public function put(string $key, $value)
    {
        $_SESSION[$key][] = $value;
    }


    public function flush()
    {
        unset($_SESSION);
    }

    public function delete(string $key)
    {
        if( isset($_SESSION[$key]) )
        {
            return ;
        }

        unset($_SESSION[$key]);
    }


    public function exists(string $key)
    {
        if( isset($_SESSION[$key]) )
        {
            return true;
        }

        return false;
    }

}