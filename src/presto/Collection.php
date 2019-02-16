<?php
namespace Presto;

use Presto\Mvc\Model\Model;

class Collection
{
    /** @var array */
    private $rows;

    /** @var int */
    private $count;


    public function __construct(array $rows=[])
    {
        $this->rows = $rows;
        $this->count = count($this->rows);
    }


    /**
     * 全部取得
     * @return array
     */
    public function all()
    {
        return $this->rows;
    }


    /**
     * 指定条件で一覧の取得
     * @param array $condition
     * @return array
     */
    public function get(array $condition)
    {
        return $this->where($condition)->all();
    }


    /**
     * 指定条件で先頭の1個を取得
     * @param array $condition
     * @return array|mixed
     */
    public function first(array $condition)
    {
        $result = $this->where($condition, 1)->all();

        return empty($result[0]) ? [] : $result[0];
    }


    /**
     * 指定条件で最後の1個を取得
     * @param array $condition
     * @return array|mixed
     */
    public function last(array $condition)
    {
        $result = $this->where($condition, 1, SORT_DESC)->all();

        return empty($result[0]) ? [] : $result[0];
    }


    /**
     * データ件数の取得
     * @param array $condition
     * @return number
     */
    public function count(array $condition)
    {
        return $this->where($condition)->count;
    }


    /**
     * 条件で一覧を絞る
     * @param array $condition
     * @param int $count
     * @param int $sort
     * @return \Presto\Collection
     */
    public function where(array $condition, int $count=0, int $sort=SORT_ASC)
    {
        $matches = [];
        $match_count = 0;

        // ソートする
        $rows = $sort==SORT_ASC ? $this->rows : array_reverse($this->rows);

        foreach ($rows as $row)
        {
            if(expression()->isMatch($row, $condition))
            {
                $matches[] = $row;
                $match_count ++;

                if($count && $match_count >= $count )
                {
                    break;
                }
            }
        }

        return new static($matches);
    }


    // 二つの配列を結合する TODO
    public function mapping(array $rows, array $foreigns, array $joins, string $type=Model::HAS_MANY)
    {
        foreach ($rows as $no=>$row)
        {
            foreach ($joins as $foreign_name=>$mappings)
            {
                $keys = array_keys($mappings);
                $values = array_map(function($key)use ($row){ return $row[$key]; }, $keys);
                $foreign_keys = array_values($mappings);
                $condition = array_combine($foreign_keys, $values);

                $rows[$no][$foreign_name] = ($type==Model::HAS_MANY) ? collection($foreigns)->get($condition) : collection($foreigns)->first($condition);
            }
        }

        return $rows;
    }


}