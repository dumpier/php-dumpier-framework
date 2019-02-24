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
     * 指定条件で先頭の1個を取得
     * @param array $condition
     * @return array|mixed
     */
    public function first(array $condition)
    {
        $rows = $this->condition($condition, 1)->all();
        return array_shift($rows);
    }


    /**
     * 指定条件で最後の1個を取得
     * @param array $condition
     * @return array|mixed
     */
    public function last(array $condition)
    {
        return end($this->rows);
    }


    /**
     * データ件数の取得
     * @param array $condition
     * @return number
     */
    public function count(array $condition)
    {
        return $this->condition($condition)->count;
    }


    /**
     * 指定項目の抽出
     * @param string $property
     * @return \Presto\Collection
     */
    public function column(string $name)
    {
        return new static(array_column($this->rows, $name));
    }


    /**
     * 指定項目の抽出 TODO 改良
     * @param string ...$names
     * @return \Presto\Collection
     */
    public function columns(...$names)
    {
        $rows = [];

        $columns = array_keys($this->rows);

        foreach ($this->rows as $key=>$val)
        {
            $row = [];
            foreach ($columns as $column)
            {
                if(in_array($column, $names))
                {
                    $row[$column] = $val[$column];
                }
            }

            $rows[$key] = $row;
        }

        return new static($rows);
    }


    /**
     * WHERE
     * @param string $name
     * @param mixed $expression
     * @param mixed ...$value
     * @return \Presto\Collection
     */
    public function where(string $name, $expression, ...$value)
    {
        $rows = array_filter($this->rows, function($row) use ($name, $expression, $value) { return expression()->compare($row[$name], $expression, ...$value); });
        return new static($rows);
    }


    /**
     * 条件で一覧を絞る
     * @param array $condition
     * @param int $limit
     * @param int $sort
     * @return \Presto\Collection
     */
    public function condition(array $condition)
    {
        $rows = [];
        $count = 0;
        $limit = empty($condition["limit"]) ? 0 : $condition["limit"];

        foreach ($this->rows as $row)
        {
            if(expression()->isMatch($row, $condition))
            {
                $rows[] = $row;
                $count ++;

                if($limit && $count >= $limit )
                {
                    break;
                }
            }
        }

        return new static($rows);
    }


    /**
     * 配列の一部を展開する
     * @return \Presto\Collection
     */
    public function slice(int $limit, int $offset=0)
    {
        $rows = array_slice(array_values($this->rows), $offset, $limit);

        return new static($rows);
    }


    /**
     * 配列を指定サイズで再分割する
     * @param int $size
     * @param bool $preserve
     * @return \Presto\Collection
     */
    public function chunk(int $size, bool $preserve=false)
    {
        return new static(array_chunk($this->rows, $size, $preserve));
    }


    /**
     * 並び替え
     * @param string $property
     * @param int $sort
     * @return \Presto\Collection
     */
    public function sort(string $property, int $sort=SORT_ASC)
    {
        $properties = array_column($this->rows, $property);

        $rows = array_multisort($properties, $sort, $this->rows);

        return new static($rows);
    }

}