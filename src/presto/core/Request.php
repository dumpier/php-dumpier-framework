<?php
namespace Presto\Core;

use Presto\Core\Traits\Singletonable;

class Request
{
    use Singletonable;

    protected $is_cli = false;
    protected $is_ajax = false;

    /** @var array 入力パラメータ一覧 */
    protected $inputs;

    public function __construct()
    {
        if( php_sapi_name() == 'cli' )
        {
            $this->is_cli = true;
            $this->inputs = $_SERVER["argv"];
        }
        else
        {
            // ajax
            $this->is_ajax = http()->isAjax();
            $this->inputs = $_REQUEST;
        }
    }


    /**
     * 入力値の取得
     * @param string $name
     * @param mixed $default_value
     * @return mixed
     */
    public function input(string $name="", $default_value=null)
    {
        if(empty($name))
        {
            return $this->inputs;
        }

        return isset($this->inputs[$name]) ? $this->inputs[$name] : $default_value;
    }


    /**
     * URIの取得
     */
    public function uri()
    {
        return http()->uri();
    }


    /**
     * Ajax判定
     * @return boolean
     */
    public function isAjax()
    {
        return $this->is_ajax;
    }


    /**
     * コマンドラインかの判定
     */
    public function isCli()
    {
        return $this->is_cli;
    }


}