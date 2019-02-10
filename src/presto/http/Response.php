<?php
namespace Presto\Http;

use Presto\Traits\Singletonable;

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


    public function header(string $key, $values, $replace = true)
    {

    }
}