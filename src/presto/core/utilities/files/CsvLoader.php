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


    public function isCsv(string $path)
    {
        if(pathinfo($path, PATHINFO_EXTENSION) == 'csv')
        {
            return true;
        }

        return false;
    }

    // CSVボディーの取得
    public function getBody(string $csvfile)
    {
        return $this->load($csvfile)["body"];
    }

    // CSヘッダーの取得
    public function getHeaders(string $csvfile)
    {
        return $this->load($csvfile)["header"];
    }

    // CSV項目一覧の取得
    public function getFields(string $csvfile)
    {
        return $this->getHeaders($csvfile)[self::HEADER_LINE_NO_FIELD];
    }


    // -------------------------------------------------------
    // 検索
    // -------------------------------------------------------
    public function findById(string $csvfile, int $id)
    {
        return $this->findFirst($csvfile, ["condition"=>['id'=>$id]]);
    }

    // 検索
    public function find(string $csvfile, array $parameter=[])
    {
        // 全CSVデータを取得
        $rows = $this->getBody($csvfile);

        if(empty($parameter["condition"]))
        {
            return $rows;
        }

        return Collection::instance($rows)->condition($parameter["condition"])->all();
    }


    // 先頭の１個を取得
    public function findFirst(string $csvfile, array $parameter=[])
    {
        $rows = $this->getBody($csvfile);

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
     * @param string $csvfile
     * @param int $page
     * @param array $parameter
     */
    public function paging(string $csvfile, int $page=1, array $parameter=[])
    {
        $rows = $this->find($csvfile, $parameter);

        list($rows, $count) = Pager::instance()->page($page)->paging($rows);
        $fields = $this->getFields($csvfile);

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
    private function load(string $csvfile)
    {
        if(! empty($this->caches[$csvfile]))
        {
            return $this->caches[$csvfile];
        }

        Debugbar::instance()->timelines("Start load csv file .", [$csvfile]);

        $spl = new \SplFileObject($csvfile);
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

        $this->caches[$csvfile]["header"] = $headers;
        $this->caches[$csvfile]["body"] = array_slice($body, self::HEADER_LINE_COUNT);

        Debugbar::instance()->timelines("Load csv file completed .", [$csvfile]);

        return $this->caches[$csvfile];
    }

}