<?php
namespace Presto\Databases\Model\Manages;

class RelationManage
{
    /** @var Relation[] */
    protected $relations = [];

    public function __construct(array $relations)
    {
        foreach ($relations as $alias=>$relation)
        {
            $this->add($alias, $relation);
        }
    }


    /**
     * リレーションが定義されてあるか
     * @return boolean
     */
    public function isUseRelations()
    {
        if(empty($this->relations))
        {
            return false;
        }

        return true;
    }


    /**
     * リレーション定義の追加
     * @param string $alias
     * @param array $relation
     * @return \Presto\Databases\Model\Manages\RelationManage
     */
    public function add(string $alias, array $relation)
    {
        $this->relations[$alias] = new Relation($alias, $relation);
        return $this;
    }


    /**
     * リレーション定義一覧の取得
     * @return \Presto\Databases\Model\Manages\Relation[]
     */
    public function all()
    {
        return $this->relations;
    }


    /**
     * 指定リレーション定義の取得
     * @param string $alias
     * @return \Presto\Databases\Model\Manages\Relation
     */
    public function get(string $alias)
    {
        return $this->relations[$alias];
    }
}

