<?php
namespace Presto\Core\Databases\Model;

use Presto\Core\Traits\Singletonable;
use Presto\Core\Databases\QueryBuilder;
use Presto\Core\Utilities\Collection;
use Presto\Core\Request;
use Presto\Core\Helpers\Html\PagerTag;
use Presto\Core\Utilities\Paginator;

class Repository
{
    use Singletonable;
    use RepositoryRelationTrait;

    const HAS_ONE = "has-one";
    const HAS_MANY = "has-many";


    /** @var string|Model モデルクラス */
    protected $class;

    /** @var Model モデルのインスタンス */
    protected $model;


    protected $slices = [];
    protected $scopes = [];
    protected $relations = [];


    public function __construct()
    {
        $this->model = $this->class::instance();
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

        $rows = QueryBuilder::instance()->connect($connection)->select($table, $parameter);
        $collection = Collection::instance($rows, $this->class);

        $recursion = empty($parameter["recursion"]) ? $recursion : $parameter["recursion"];

        if( empty($recursion)  )
        {
            return $collection;
        }

        // リレーションのロード
        return $this->loadRelations($collection, $recursion);
    }

    /**
     * カウント
     * @param array $parameter
     * @return number
     */
    public function count(array $parameter=[])
    {
        $connection = $this->model->getConnection();
        $table = $this->model->getTable();
        return QueryBuilder::instance()->connect($connection)->count($table, $parameter);
    }


    /**
     * 主キーで検索
     * @param int $primary_key
     * @param int $recursion
     * @return Model|NULL
     */
    public function findByPk(int $primary_key, int $recursion=0)
    {
        $primary_key_name = $this->model->getPrimaryKey();
        $parameter = [];
        $parameter["condition"][$primary_key_name] = $primary_key;
        $parameter["limit"] = 1;

        $rows = $this->find($parameter, $recursion);

        return $rows->first();
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

        return $rows->first();
    }


    /**
     * ページング
     * @param array $parameter
     * @param int $recursion
     * @return \Presto\Core\Utilities\Paginator
     */
    public function paging(array $parameter=[], int $recursion=0)
    {
        $page = (int)Request::instance()->input("page", 1);
        $count = $this->count($parameter);

        list($start, ) = PagerTag::instance()->getStartEndRowNumber($count, $page);
        $parameter["offset"] = $start;
        $parameter["limit"] = PagerTag::LIMIT_COUNT;

        $rows = $this->find($parameter, $recursion);

        return Paginator::instance($rows)->count($count)->page($page);
    }

}