<?php
namespace Presto\Core\Databases\Model;


use Presto\Core\Traits\Towable;
use Presto\Core\Traits\Instanceable;
use Presto\Core\Databases\QueryBuilder;

class Model
{
    use Instanceable, Towable;

    const HAS_ONE = 'has-one';
    const HAS_MANY = 'has-many';


    /** 主キー TODO 複合主キーの対応 */
    const PRIMARY_KEY = 'id';

    /** 作成日時 */
    const COLUMN_CREATE_AT = 'created';

    /** 更新日時 */
    const COLUMN_UPDATE_AT = 'modified';

    /** 論理削除フラグ */
    const COLUMN_DELETED = 'deleted';

    /** 排他制御用項目 TODO 未実装 */
    const COLUMN_EXCLUSIVE = "exclusive";

    protected $connection;
    protected $database;
    protected $table;

    protected $properties = [];
    protected $originals;
    protected $relations;

    /** @var int 直近のクエリで使用した自動生成のID */
    private $last_insert_id = 0;

    /**
     * Connection名の取得
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }


    /**
     * テーブル名の取得
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }


    /**
     * 主キーの取得
     * TODO 複合主キーの対応
     * @return string
     */
    public function getPrimaryKey()
    {
        return static::PRIMARY_KEY;
    }

    /**
     * 主キー値の取得
     * @return mixed
     */
    public function getPrimaryValue()
    {
        if(empty($this->{static::PRIMARY_KEY}))
        {
            return null;
        }

        return $this->{static::PRIMARY_KEY};
    }


    /**
     * 直近のクエリで使用した自動生成のIDを返す
     * @return number
     */
    public function getLastInsertId()
    {
        return $this->last_insert_id;
    }


    /**
     * 新しいレコードをINSERTする
     * @param array $row
     * @throws \Exception
     * @return \Presto\Core\Databases\Model\Model
     */
    public function create(array $row=[])
    {
        if( $this->getPrimaryValue() )
        {
            throw new \Exception("既存モデルからは新規追加できない。");
        }

        $row = empty($row) ? $this->toArray() : $row;

        // TODO last_insert_idの整理
        $this->last_insert_id = QueryBuilder::instance()->connect($this->connection)->insert($this->table, $row);
        $this->{static::PRIMARY_KEY} = $this->last_insert_id;

        return $this;
    }


    public function update(array $row=[])
    {
        QueryBuilder::instance()->connect($this->connection)->update($this->table, $row, [static::PRIMARY_KEY =>$this->getPrimaryValue()]);
        return $this;
    }


    public function save(array $row=[])
    {
        if( $this->getPrimaryValue())
        {
            return $this->update($row);
        }

        return $this->create($row);
    }


    public function delete()
    {
        QueryBuilder::instance()->connect($this->connection)->delete($this->table, [static::PRIMARY_KEY =>$this->getPrimaryValue()]);
    }


    public function hide()
    {
        QueryBuilder::instance()->connect($this->connection)->update($this->table, [static::COLUMN_DELETED=>TRUE], [static::PRIMARY_KEY =>$this->getPrimaryValue()]);
        return $this;
    }


    public function show()
    {
        QueryBuilder::instance()->connect($this->connection)->update($this->table, [static::COLUMN_DELETED=>FALSE], [static::PRIMARY_KEY =>$this->getPrimaryValue()]);
        return $this;
    }

}