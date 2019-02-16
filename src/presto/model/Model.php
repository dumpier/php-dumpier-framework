<?php
namespace Presto\Model;


class Model
{
    protected $connection;
    protected $database;
    protected $table;

    protected $originals;
    protected $relations;

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
        return self::PRIMARY_KEY;
    }

    /**
     * 主キー値の取得
     * @return mixed
     */
    public function getPrimaryValue()
    {
        return $this->{self::PRIMARY_KEY};
    }


    /**
     * 新しいレコードをINSERTする
     * @param array $row
     * @throws \Exception
     * @return \Presto\Model\Model
     */
    public function create(array $row=[])
    {
        if( $this->getPrimaryValue() )
        {
            throw new \Exception("既存モデルからは新規追加できない。");
        }

        database($this->connection)->insert($this->table, $row);
        return $this;
    }

    public function update(array $row=[])
    {
        database($this->connection)->update($this->table, $row, [self::PRIMARY_KEY =>$this->getPrimaryValue()]);
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
        database($this->connection)->delete($this->table, [self::PRIMARY_KEY =>$this->getPrimaryValue()]);
    }


    public function hide()
    {
        database($this->connection)->update($this->table, [self::COLUMN_DELETED=>TRUE], [self::PRIMARY_KEY =>$this->getPrimaryValue()]);
        return $this;
    }


    public function show()
    {
        database($this->connection)->update($this->table, [self::COLUMN_DELETED=>FALSE], [self::PRIMARY_KEY =>$this->getPrimaryValue()]);
        return $this;
    }

}