<?php
namespace Presto\Databases;

use Presto\Traits\Singletonable;
use Presto\Paginator;
use Presto\Helpers\Paging;

class QueryBuilder
{
    use Singletonable;

    protected $driver = "mysql";

    /** @var array|\mysqli */
    protected $conns = [];

    /** @var string 現在接続中 */
    protected $current_conn ="";

    /** @var string 現在接続中 */
    protected $current_database ="";


    /**
     * テーブル一覧の取得
     * @return array|mixed
     */
    public function tables()
    {
        return $this->querySelect("SHOW TABLE STATUS");
    }

    /**
     * テーブルの項目一覧詳細の取得
     * @param string $table
     * @return array|mixed
     */
    public function columns(string $table)
    {
        return $this->querySelect("SHOW FULL COLUMNS FROM `{$table}`");
    }


    /**
     * 検索
     * TODO テーブル名のSQL INJECTION対応
     * @param string $table
     * @param array $parameters
     * @return array|mixed
     */
    public function select(string $table, array $parameters=[])
    {
        list($where, $binds) = $this->where($parameters);

        $fields = empty($parameters["fields"]) ? "*" : implode(",", $parameters["fields"]);

        $offset = empty($parameters["offset"]) ? 0 : $parameters["offset"];
        $limit = empty($parameters["limit"]) ? 0 : $parameters["limit"];
        $sql_offset = empty($limit) ? "" : " LIMIT {$offset}, {$limit}";

        $sql = "SELECT {$fields} FROM `{$table}` {$where} {$sql_offset}";

        return $this->querySelect($sql, $binds);
    }


    /**
     * ページング
     * @param string $table
     * @param array $parameters
     * @return \Presto\Paginator
     */
    public function paging(string $table, array $parameters=[])
    {
        $page = input("page", 1);
        $count = $this->count($table, $parameters);

        list($start, ) = paging()->getStartEndRowNumber($count, $page);
        $parameters["offset"] = $start;
        $parameters["limit"] = Paging::LIMIT_COUNT;
        $rows = $this->select($table, $parameters);

        return new Paginator($rows, $count, $page);
    }


    /**
     * カウント
     * @param string $table
     * @param array $parameters
     * @return int
     */
    public function count(string $table, array $parameters=[])
    {
        list($where, $binds) = $this->where($parameters);
        $field = empty($parameters["count_field"]) ? "*" : $parameters["count_field"];

        $sql = "SELECT COUNT({$field}) AS `count` FROM `{$table}` {$where}";

        $row = $this->querySelect($sql, $binds, 1);
        return $row["count"];
    }


    /**
     * 合計
     * @param string $table
     * @param array $parameters
     * @throws \Exception
     * @return array|mixed
     */
    public function sum(string $table, array $parameters=[])
    {
        if(empty($parameters["fields"]))
        {
            throw new \Exception("合計する項目を指定してください");
        }

        $sum_fields = "";
        foreach ($parameters["fields"] as $field)
        {
            $sum_fields .= ", SUM(`{$field}`) AS `{$field}`";
        }
        $sum_fields = ltrim($sum_fields, ",");

        list($where, $binds) = $this->where($parameters);
        $sql = "SELECT {$sum_fields} FROM `{$table}` {$where}";

        return $this->querySelect($sql, $binds, 1);
    }


    public function insert(string $table, array $row)
    {
        $binds = array_values($row);
        $sql_column = implode("`,`", array_keys($row));
        $sql_values = trim(str_repeat( '?,', count($row)), ",");

        $sql = "INSERT INTO `{$table}` (`{$sql_column}`) VALUES ({$sql_values})";

        return $this->query($sql, $binds);
    }


    public function update(string $table, array $row, array $condition)
    {
        $values = array_values($row);
        $sql_set = trim(implode("`= ?,`", array_keys($row)), ",`");
        list($where, $binds) = where($condition);

        foreach ($binds as $val)
        {
            $values[] = $val;
        }

        $sql = "UPDATE `{$table}` SET `{$sql_set}`=? {$where}";

        return $this->query($sql, $values);
    }


    public function delete(string $table, array $condition)
    {
        list($where, $binds) = where($condition);
        $sql = "DELETE FROM `{$table}` {$where}";

        return $this->query($sql, $binds);
    }


    /**
     * SELECT以外
     * @param string $sql
     * @param array $binds
     * @param int $count
     * @param bool $is_select
     * @return integer
     */
    private function query(string $sql, array $binds=[])
    {
        $stmt = $this->execute($sql, $binds);
        debugbar()->queries($sql, $binds);

        return $stmt->errno;
    }


    /**
     * SELECT系
     * @param string $sql
     * @param array $binds
     * @param int $count
     * @return mixed|array
     */
    private function querySelect(string $sql, array $binds=[], int $count=0)
    {
        $stmt = $this->execute($sql, $binds);

        $result = $stmt->get_result();

        // 1行のみ取得の場合
        if($count == 1)
        {
            debugbar()->queries($sql, $binds);
            return $result->fetch_array(MYSQLI_ASSOC);
        }

        // 全部取得の場合
        $rows = [];
        while($row = $result->fetch_array(MYSQLI_ASSOC)) {
            array_push($rows, $row);
        }

        debugbar()->queries($sql, $binds);
        return $rows;
    }


    /**
     * SQLの実行
     * @param string $sql
     * @param array $binds
     * @throws \Exception
     * @return \mysqli_stmt
     */
    private function execute(string $sql, array $binds=[])
    {
        debugbar()->timerstart();
        $conn = $this->connection($this->current_conn, $this->current_database);
        $stmt = $conn->prepare($sql);

        if(FALSE === $stmt)
        {
            throw new \Exception($conn->error, $conn->errno);
        }

        // バインド処理
        if(!empty($binds))
        {
            $params = array( str_repeat( 's', count($binds) ) );

            foreach ($binds as $key=>$val)
            {
                $params[] = &$binds[$key];
            }

            call_user_func_array(array($stmt, 'bind_param'), $params);
        }

        // TODO 処理結果の判定
        $return = $stmt->execute();

        return $stmt;
    }


    // ----------------------------------------------------------
    // 接続関連
    // ----------------------------------------------------------
    /**
     * 接続の取得
     * @param string $name
     * @return \mysqli
     */
    public function connection(string $name="", string $database="")
    {
        list($name, $database, $config) = $this->config($name, $database);

        if(empty($this->conns[$name][$database]))
        {
            $conn = new \mysqli($config['host'], $config['user'], $config['pass'], $config['db'], $config['port']);

            if($conn->connect_error)
            {
                throw new \Exception("Mysql接続エラー[{$name}]\n{$conn->connect_error}");
            }

            $this->current_conn = $name;
            $this->current_database = $database;
            $this->conns[$name][$database] = $conn;
        }

        return $this->conns[$name][$database];
    }


    // 接続する
    public function connect(string $name="", string $database="")
    {
        $this->connection($name, $database);
        return $this;
    }

    // conditions配列からWHEREとBINDの取得
    private function where(array $parameters=[])
    {
        return empty($parameters["conditions"]) ? ["",[]] : where($parameters["conditions"]);
    }

    // database.configの取得
    private function config(string $name="", string $database="")
    {
        $name = empty($name) ? config("database", "default") : $name;

        $config = config("database", "connections.{$name}");

        $config['host'] = empty($config['host']) ? "127.0.0.0" : $config['host'];
        $config['port'] = empty($config['port']) ? 3306 : $config['port'];
        $config['db'] = empty($config['db']) ? $database : $config['db'];

        if(empty($config["db"]))
        {
            throw new \Exception("[connection:{$name}]dbが見当たらない");
        }

        if(empty($config["user"]) || empty($config["pass"]))
        {
            throw new \Exception("[connection:{$name}]user, passが見当たらない");
        }

        return [$name, $config['db'], $config];
    }
    // ----------------------------------------------------------

}
