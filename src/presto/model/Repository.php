<?php
namespace Presto\Model;

use Presto\Traits\Singletonable;
use Presto\Model\RepositoryRelationTrait;

class Repository
{
    use Singletonable;
    use RepositoryRelationTrait;

    const HAS_ONE = "has-one";
    const HAS_MANY = "has-many";


    /** @var string モデルクラス */
    protected $class;

    /** @var Model モデルのインスタンス */
    protected $model;


    protected $slices = [];
    protected $scopes = [];
    protected $relations = [];


    public function __construct()
    {
        $this->model = new $this->class;
    }


    /**
     * テーブル名の取得
     * @return string
     */
    public function getTable()
    {
        return $this->model->getTable();
    }


    /**
     * 検索
     * @param array $parameters
     * @param int $recursion
     * @return array
     */
    public function find(array $parameters=[], int $recursion=0)
    {
        $connection = $this->model->getConnection();
        $table = $this->model->getTable();

        $rows = database($connection)->select($table, $parameters);

        if( empty($recursion) && empty($parameters["recursion"]))
        {
            return $rows;
        }

        // リレーションのロード
        return $this->loadRelations($rows);
    }


    /**
     * 主キーで検索
     * @param int $primary_key
     * @param int $recursion
     * @return array|mixed
     */
    public function findByPk(int $primary_key, int $recursion=0)
    {
        $primary_key_name = $this->model->getPrimaryKey();
        $parameters = [];
        $parameters["conditions"][$primary_key_name] = $primary_key;
        $parameters["limit"] = 1;

        return $this->find($parameters, $recursion);
    }


    /**
     * 先頭の1個を取得
     * @param array $parameters
     * @param int $recursion
     * @return array|mixed
     */
    public function findFirst(array $parameters=[], int $recursion=0)
    {
        $parameters["limit"] = 1;
        return $this->find($parameters, $recursion);
    }

}