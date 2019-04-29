<?php
namespace Presto\Core\Utilities\Files;

use Presto\Core\Traits\Singletonable;
use Presto\Core\Utilities\Pager;
use Presto\Core\Utilities\Collection;

class JsonLoader
{
    use Singletonable;

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


    // CSヘッダーの取得
    public function getHeaders(string $file)
    {
        return $this->load($file)["header"];
    }

    // CSV項目一覧の取得
    public function getFields(string $file)
    {
        return $this->getHeaders($file);
    }

    public function getBody(string $file)
    {
        return $this->load($file)["body"];
    }


    private $caches = [];
    private function load(string $file)
    {
        if(! empty($this->caches[$file]))
        {
            return $this->caches[$file];
        }

        $contents = file($file);

        $rows = [];
        foreach ($contents as $line)
        {
            $rows[] = json_decode($line, TRUE);
        }

        $this->caches[$file]["header"] = array_keys($rows[0]);
        $this->caches[$file]["body"] = $rows;

        return $this->caches[$file];
    }
}