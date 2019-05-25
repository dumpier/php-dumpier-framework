<?php
namespace Presto\Core\Utilities\Files;

use Presto\Core\Traits\Singletonable;
use Presto\Core\Utilities\Pager;

class FileLoader
{
    use Singletonable;

    const CACHE_LINECOUNT = "linecount";
    const CACHE_SIZE = "size";
    private $cachs = [];

    public function isCsv(string $filename) { return $this->extension($filename) == "csv"; }
    public function isJson(string $filename) { return $this->extension($filename) == "json"; }
    public function isIni(string $filename) { return $this->extension($filename) == "ini"; }
    public function isYaml(string $filename) { return $this->extension($filename) == "yaml"; }

    /**
     * ファイル拡張子の取得
     * @param string $path
     * @return mixed
     */
    public function extension(string $filename) { return pathinfo($filename, PATHINFO_EXTENSION); }

    /**
     * ファイルサイズの取得
     * @param string $filename
     * @return number
     */
    public function byte(string $filename)
    {
        if( ! isset ($this->cachs[$filename][self::CACHE_SIZE]) )
        {
            $this->cachs[$filename][self::CACHE_SIZE] = filesize($filename);
        }

        return $this->cachs[$filename][self::CACHE_SIZE];
    }


    /**
     * ファイル行数の取得
     * @param string $filename
     */
    public function line(string $filename)
    {
        if( ! isset ($this->cachs[$filename][self::CACHE_LINECOUNT]) )
        {
            $this->cachs[$filename][self::CACHE_LINECOUNT] = $this->getLineCount($filename);
        }

        return $this->cachs[$filename][self::CACHE_LINECOUNT];
    }

    /**
     * 全部読み込み
     * @param string $filename
     * @return string
     */
    public function all(string $filename) { return file_get_contents($filename); }


    /**
     * ページング
     * @param string $filename
     * @param int $page
     * @return string[]
     */
    public function paging(string $filename, int $page=1)
    {
        $lines = [];

        // 行数の取得
        $linecount = $this->line($filename);

        list($start, $end) = Pager::instance()->count($linecount)->page($page)->getStartEndRowNumber();

        $fp = fopen($filename, 'r');
        for ($i = $start; $i < $end; $i++)
        {
            $lines[] = fgets($fp);
        }
        fclose($fp);

        return [$lines, $linecount];
    }

    // TODO ファイル行数の取得
    private function getLineCount(string $filename)
    {
        debugbar()->timelines("Start get line count");

        $count = exec('wc -l '.$filename);
        $count = trim(str_replace($filename, '', $count));

//         $fp = fopen( $filename, 'r' );
//         for( $count = 0; fgets( $fp); $count++ );
//         fclose($fp);

        debugbar()->timelines("Get line count completed !");

        return $count;
    }



}