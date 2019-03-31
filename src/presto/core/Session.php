<?php
namespace Presto\Core;

use Presto\Core\Traits\Singletonable;

class Session
{
    use Singletonable;

    /**
     * 全部取得
     * @return mixed
     */
    public function all()
    {
        return $_SESSION;
    }


    /**
     * 指定キーのセッションを取得
     * @param string $key
     * @return NULL|mixed
     */
    public function get(string $key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }


    /**
     * セッションの設定
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
        return $this;
    }


    /**
     * セッションキーに値を追加
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function put(string $key, $value)
    {
        $_SESSION[$key][] = $value;
        return $this;
    }


    /**
     * 指定キーのセッションを削除
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function delete(string $key)
    {
        if( isset($_SESSION[$key]) )
        {
            return $this;
        }

        unset($_SESSION[$key]);
        return $this;
    }


    /**
     * セッションを全部削除
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function flush()
    {
        unset($_SESSION);
        return $this;
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