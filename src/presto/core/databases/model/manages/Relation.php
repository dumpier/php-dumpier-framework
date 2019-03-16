<?php
namespace Presto\Core\Databases\Model\Manages;

use Presto\Core\Databases\Model\Repository;

class Relation
{
    public $alias;
    public $repository;

    public $type;
    public $where;
    public $join;
    public $condition;


    public function __construct(string $alias, array $relation)
    {
        if(empty($relation["join"]))
        {
            throw new \Exception("relationsのjoinが定義されてない." . get_class($this));
        }

        $this->type = $relation["type"];
        $this->alias = $alias;
        $this->repository = $relation["repository"];

        $this->join = $relation["join"];
        $this->where = empty($relation["where"]) ? [] : $relation["where"];
        $this->condition = empty($relation["condition"]) ? [] : $relation["condition"];
    }


    /**
     * リポジトリクラス名の取得
     * @return Repository
     */
    public function getRepository()
    {
        return new $this->repository;
    }


    /**
     * JOINエリアス名の取得
     * @return string
     */
    public function getAlias()
    {
        if(class_exists($this->alias))
        {
            return $this->getRepository()->getTable();
        }

        return $this->alias;
    }


    public function isWhereTarget(array $row)
    {
        if(empty($this->where))
        {
            return true;
        }

        return expression()->isMatch($row, $this->where);
    }
}
