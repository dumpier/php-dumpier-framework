<?php
namespace Presto;

use Presto\Traits\Singletonable;

class Request
{
    use Singletonable;

    protected $is_cli = false;
    protected $is_ajax = false;

    /** @var array 入力パラメータ一覧 */
    protected $inputs;


    /**
     * 入力値の取得
     * @param string $name
     * @param mixed $default_value
     * @return mixed|string
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


    // 初期化処理
    protected function init()
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

}