<?php
namespace Presto\Core\Protocols;

use Presto\Core\Traits\Singletonable;
use Presto\Core\Presto;

class Http
{
    use Singletonable;

    const CODE_200 = 200;
    const CODE_302 = 302;
    const CODE_400 = 400;
    const CODE_401 = 401;
    const CODE_402 = 402;
    const CODE_403 = 403;
    const CODE_404 = 404;
    const CODE_500 = 500;


    const HEADER_CONTENT_TYPE = "Content-Type";


    /**
     * HOST名の取得
     * @return string
     */
    public function host()
    {
        return $_SERVER['HTTP_HOST'];
    }


    public function isUrl(string $url)
    {
        if(preg_match("/^[http|https]/", $url))
        {
            return true;
        }

        return false;
    }


    /**
     * URL
     * @param string $url
     * @return string
     */
    public function url(string $url=null)
    {
        $url = $url ?? $_SERVER["REQUEST_URI"];

        if(preg_match("/^[http|https]/", $url))
        {
            return $url;
        }

        $host = $this->host();
        $protocol = $this->protocol();

        return "{$protocol}://{$host}{$url}";
    }


    /**
     * URI
     * @param string $url
     * @return string
     */
    public function uri(string $uri=null)
    {
        $uri = $uri ?? $_SERVER["REQUEST_URI"];

        // ベースURIを除く
        $uri = preg_replace("/^" . preg_quote(Presto::instance()->baseuri()) . "/", "", $uri);
        $uri = preg_replace("/\?.*/", "", $uri);

        return $uri;
    }


    /**
     * https通信であるか
     * @param string $url
     * @return number|boolean
     */
    public function isHttps(string $url="")
    {
        if($url)
        {
            return preg_match("/^https/", $url);
        }

        if(empty($_SERVER["HTTPS"]))
        {
            return false;
        }

        return true;
    }


    /**
     * httpsかhttpのいずれを返す
     * @param string $url
     * @return string https|http
     */
    public function protocol(string $url="")
    {
        if($this->isHttps($url))
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
        if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') )
        {
            return true;
        }

        return false;
    }

}