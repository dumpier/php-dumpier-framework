<?php
namespace Presto\Core\Utilities\Files;

use Presto\Core\Traits\Singletonable;
use Presto\Core\Utilities\Collection;
use Presto\Core\Utilities\Debugbar;
use Presto\Core\Utilities\Pager;

class CsvLoader
{
    use Singletonable;

    /** ヘッダーの行数 */
    const HEADER_LINE_COUNT = 1;

    /** 項目名の行番号 */
    const HEADER_LINE_NO_FIELD = 0;


    // CSVボディーの取得
    public function getBody(string $file)
    {
        return $this->load($file)["body"];
    }

    // CSヘッダーの取得
    public function getHeader(string $file)
    {
        return $this->load($file)["header"];
    }

    // CSV項目一覧の取得
    public function getFields(string $file)
    {
        return $this->getHeader($file)[self::HEADER_LINE_NO_FIELD];
    }


    // -------------------------------------------------------
    // 検索
    // -------------------------------------------------------
    public function findById(string $file, int $id)
    {
        return $this->findFirst($file, ["condition"=>['id'=>$id]]);
    }

    // 検索
    public function find(string $file, array $parameter=[])
    {
        // 全CSVデータを取得
        $rows = $this->getBody($file);

        if(empty($parameter["condition"]))
        {
            return $rows;
        }

        return Collection::instance($rows)->condition($parameter["condition"])->all();
    }


    // 先頭の１個を取得
    public function findFirst(string $file, array $parameter=[])
    {
        $rows = $this->getBody($file);

        if(empty($parameter["condition"]))
        {
            return empty($rows[0]) ? [] : $rows[0];
        }

        return Collection::instance($rows)->first($parameter["condition"]);
    }


    // 最後の１個を取得
    public function findLast(string $csvfile, array $parameter=[])
    {
        $rows = $this->getBody($csvfile);

        if(empty($parameter["condition"]))
        {
            $last = end($rows);
            return empty($last) ? [] : $last;
        }

        return Collection::instance($rows)->last($parameter["condition"]);
    }


    /**
     *  ページング
     * @param string $file
     * @param int $page
     * @param array $parameter
     */
    public function paging(string $file, int $page=1, array $parameter=[])
    {
        $rows = $this->find($file, $parameter);

        list($rows, $count) = Pager::instance()->page($page)->paging($rows);
        $fields = $this->getFields($file);

        return [$rows, $count, $fields];
    }
    // -------------------------------------------------------


    /**
     * csvのキャッシュ
     * @var array
     */
    private $caches = [];

    /**
     * csvのロード
     * @param string $csvfile
     * @return mixed
     */
    private function load(string $file)
    {
        if(! empty($this->caches[$file]))
        {
            return $this->caches[$file];
        }

        Debugbar::instance()->timelines("Start load csv file .", [$file]);

        $spl = new \SplFileObject($file);
        $spl->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

        // ヘッダーの取得
        $headers = [];
        foreach ($spl as $line=>$row)
        {
            if($line >= self::HEADER_LINE_COUNT)
            {
                break;
            }
            $headers[] = $row;
        }

        // ヘッダーから項目物理名一覧の取得
        $fields = $headers[self::HEADER_LINE_NO_FIELD];

        // CSVのBodyを連想配列に変更
        $body = [];
        foreach ($spl as $line=>$row)
        {
            $body[] = array_combine($fields, $row);
        }

        $this->caches[$file]["header"] = $headers;
        $this->caches[$file]["body"] = array_slice($body, self::HEADER_LINE_COUNT);

        Debugbar::instance()->timelines("Load csv file completed .", [$file]);

        return $this->caches[$file];
    }

}