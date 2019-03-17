<?php
namespace Presto\Core;

use Presto\Core\Traits\Singletonable;
use Presto\Core\Protocols\Http;

class Response
{
    use Singletonable;

    const HTML = 'html';
    const JSON = 'json';
    const JSONP = 'jsonp';
    const TEXT = 'text';
    const FILE = 'file';
    const STREAM = 'stream';
    const STREAM_DOWNLOAD = 'streamDownload';
    const DOWNLOAD = 'download';

    protected $type;

    protected $headers = [];

    public function header(string $key, $values, $replace = true)
    {
        return $this;
    }


    /**
     * リダイレクト
     * @param string $url
     * @param int $code
     */
    public function redirect(string $url, int $code=Http::CODE_302)
    {
        header("Location: $url", TRUE, $code);
        exit;
    }

    public function charaset(string $charaset)
    {

    }

    public function contentType(string $content_type)
    {

    }
    /*
     * header("Content-Type: application/json; charset=utf-8");
     *
     */


}