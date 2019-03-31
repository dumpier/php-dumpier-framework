<?php
namespace Presto\Core\Databases\Model;

use Presto\Core\Traits\Singletonable;

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
     * @param array $parameter
     * @param int $recursion
     * @return array
     */
    public function find(array $parameter=[], int $recursion=0)
    {
        $connection = $this->model->getConnection();
        $table = $this->model->getTable();

        $rows = database($connection)->select($table, $parameter);

        if( empty($recursion) && empty($parameter["recursion"]))
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
        $parameter = [];
        $parameter["condition"][$primary_key_name] = $primary_key;
        $parameter["limit"] = 1;

        $rows = $this->find($parameter, $recursion);

        return empty($rows[0]) ? null : $rows[0];
    }


    /**
     * 先頭の1個を取得
     * @param array $parameter
     * @param int $recursion
     * @return array|mixed
     */
    public function findFirst(array $parameter=[], int $recursion=0)
    {
        $parameter["limit"] = 1;
        $rows = $this->find($parameter, $recursion);

        return empty($rows[0]) ? null : $rows[0];
    }


    /**
     * ページング
     * @param array $parameter
     * @param int $recursion
     * @return \Presto\Core\Utilities\Paginator
     */
    public function paging(array $parameter=[], int $recursion=0)
    {
        $connection = $this->model->getConnection();
        $table = $this->model->getTable();

        return database($connection)->paging($table, $parameter);
    }
}