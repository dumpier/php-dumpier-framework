<?php
namespace Presto\Mvc\Model;

use Presto\Mvc\Model\Manages\RelationManage;
use Presto\Mvc\Model\Manages\Relation;
use Presto\Mvc\Model\Manages\SliceManage;
use Presto\Mvc\Model\Manages\ScopeManage;

/**
 * @property array $slices
 * @property array $scopes
 * @property array $relations
 */
trait RepositoryRelationTrait
{
    /** @var \Presto\Mvc\Model\Manages\RelationManage */
    protected  $relationManage;

    /** @var \Presto\Mvc\Model\Manages\SliceManage */
    protected  $sliceManage;

    /** @var \Presto\Mvc\Model\Manages\ScopeManage */
    protected  $scopeManage;


    /**
     * 全リレーションのロード
     * @param array $rows
     * @return array|mixed
     */
    public function loadRelations(array $rows, int $recursion=0)
    {
        $this->relationManage = new RelationManage($this->relations);
        $this->sliceManage = new SliceManage($this->slices);
        $this->scopeManage = new ScopeManage($this->scopes);

        // リレーション定義がない場合、そのまま返す
        if( ! $this->relationManage->isUseRelations() )
        {
            return $rows;
        }

        // 全リレーションをループしながら、ロードする
        foreach ($this->relationManage->all() as $relation)
        {
            $rows = $this->loadRelation($rows, $relation, $recursion);
        }

        return $rows;
    }


    /**
     * 指定リレーションのロード
     * @param array $rows
     * @param Relation $relation
     * @return array|mixed
     */
    public function loadRelation(array $rows, Relation $relation, int $recursion=0)
    {
        // 子テーブルの検索条件
        $cond = $this->foreignCondition($rows, $relation);

        // 子テーブルの検索
        $foreigns = $relation->getRepository()->find(["conditions"=>$cond], $recursion);

        // 子テーブルを親に代入
        $rows = collection()->mapping($rows, $foreigns, $relation->join, $relation->type);

        return $rows;
    }


    /**
     * 子テーブルの対象項目の値一覧の抽出
     * @param array $rows
     * @param Relation $relation
     * @return array
     */
    protected function foreignCondition(array $rows, Relation $relation)
    {
        $cond = [];

        foreach ($rows as $no=>$row)
        {
            // where指定の場合は、親をフィルターする
            if(! $relation->isWhereTarget($row))
            {
                continue;
            }

            // join条件の生成
            foreach ($relation->join as $foreign_name=>$mapping)
            {
                $sub_cond = [];
                foreach ($mapping as $parent_field=>$children_field)
                {
                    $sub_cond[$children_field] = $row[$parent_field];
                }

                // TODO 同じ条件が重複されないように
                $cond["or"][] = $sub_cond;
            }
        }

        return $cond;
    }


}