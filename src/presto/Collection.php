<?php
namespace Presto;

use Presto\Traits\Instanceable;

class Collection
{
    use Instanceable;

    /** @var array */
    protected $rows;

    /** @var int */
    protected $count;


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

}