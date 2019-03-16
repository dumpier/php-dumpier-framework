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
            $this->is_ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) ? true : false;
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
        // ベースURIを除く
        $uri = preg_replace("/^" . preg_quote(baseuri()) . "/", "", $_SERVER['REQUEST_URI']);
        $uri = preg_replace("/\?.*/", "", $uri);

        return $uri;
    }


    /**
     * httpsかの判定
     * @return boolean
     */
    public function isHttps()
    {
        if(empty($_SERVER["HTTPS"]))
        {
            return false;
        }

        return true;
    }

    /**
     * httpsかhttpのいずれを返す
     * @return string https|http
     */
    public function getProtocol()
    {
        if($this->isHttps())
        {
            return "https";
        }

        return "http";
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