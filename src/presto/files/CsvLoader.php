<?php
namespace Presto\Files;

use Presto\Traits\Singletonable;

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
        return $this->findFirst($csvfile, ['conditions'=>['id'=>$id]]);
    }

    // 検索
    public function find(string $csvfile, array $parameters=[])
    {
        // 全CSVデータを取得
        $rows = $this->getBody($csvfile);

        if(empty($parameters["conditions"]))
        {
            return $rows;
        }

        return collection($rows)->get($parameters["conditions"]);
    }


    // 先頭の１個を取得
    public function findFirst(string $csvfile, array $parameters=[])
    {
        $rows = $this->getBody($csvfile);

        if(empty($parameters["conditions"]))
        {
            return empty($rows[0]) ? [] : $rows[0];
        }

        return collection($rows)->first($parameters["conditions"]);
    }


    // 最後の１個を取得
    public function findLast(string $csvfile, array $parameters=[])
    {
        $rows = $this->getBody($csvfile);

        if(empty($parameters["conditions"]))
        {
            $last = end($rows);
            return empty($last) ? [] : $last;
        }

        return collection($rows)->last($parameters["conditions"]);
    }


    /**
     *  ページング
     * @param string $csvfile
     * @param int $page
     * @param array $parameters
     */
    public function paging(string $csvfile, int $page=1, array $parameters=[])
    {
        $rows = $this->find($csvfile, $parameters);

        list($rows, $count) = paging()->paging($rows, $page);
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

        timelines("Start load csv file .", [$csvfile]);

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

        timelines("Load csv file completed .", [$csvfile]);

        return $this->caches[$csvfile];
    }

}