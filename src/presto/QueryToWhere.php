<?php
namespace Presto;

use Presto\Traits\Singletonable;

class QueryToWhere
{
    use Singletonable;

    /**
     * 配列をSQL条件式に変換する
     * @param array $conditions
     */
    public function convert(array $conditions=[])
    {
        if(empty($conditions))
        {
            return "";
        }

        list($where, $binds) = $this->toWhereString($conditions);
        $where = "WHERE " . $this->toCleanWhereString($where);

        return [$where, $binds];
    }


    private function toWhereString(array $conditions=[], array $binds=[])
    {
        $where = "";

        if(empty($conditions))
        {
            return ["", []];
        }

        foreach ($conditions as $key=>$val)
        {
            // 数字の場合は、グルーピング条件
            if(is_numeric($key))
            {
                list($sub_where, $binds) = $this->toWhereString($val, $binds);
                $where .= " AND ( {$sub_where} )";
                continue;
            }

            // ORグループの場合
            if('or' === strtolower($key))
            {
                list($sub_where, $binds) = $this->toOrWhereString($val, $binds);
                $where .= " AND {$sub_where} ";
                continue;
            }

            // 配列の場合
            if(is_array($val))
            {
                $expression = key($val);
                if( expression()->is($expression) )
                {
                    // in, not in, >, >=, <, <=, <>, !=
                    list($sub_where, $binds) = $this->toExpression($key, $expression, $val[$expression], $binds);
                    $where .= " AND {$sub_where}";
                    continue;
                }

                // 配列の再帰処理
                list($sub_where, $binds) = $this->toWhereString($val, $binds);
                $where .= $sub_where;
                continue;
            }

            if(is_object($val))
            {
                throw new \Exception("objectは未対応[key:{$key}]");
            }

            // プリミティブ型の場合
            $where .= " AND `{$key}`= ? ";
            $binds[] = $val;
        }

        $where = preg_replace("/^ *AND */", "", $where);
        return [$where, $binds];
    }


    private function toCleanWhereString(string $where)
    {
        $where = preg_replace("/^ *AND /", "", $where);
        $where = preg_replace("/ {2,}/", " ", $where);
        $where = preg_replace("/\( */", "(", $where);
        $where = preg_replace("/ *\)/", ")", $where);
        $where = trim($where);

        return $where;
    }


    private function toOrWhereString(array $conditions, array $binds=[])
    {
        $where = "";

        foreach ( $conditions as $key=>$val )
        {
            if(is_numeric($key))
            {
                list($sub_where, $binds) = $this->toWhereString($val, $binds);
                $where .= " OR ( {$sub_where} )";
                continue;
            }

            if(is_array($val))
            {
                if( expression()->is($val[0]) )
                {
                    // in, not in, >, >=, <, <=, <>, !=
                    list($sub_where, $binds) = $this->toExpression($key, $val[0], $val[1], $binds);
                    $where .= " OR {$sub_where}";
                    continue;
                }

                list($sub_where, $binds) = $this->toWhereString($conditions, $binds);

                $where .= $sub_where;
                continue;
            }

            $binds[] = $val;
            $where .= " OR `{$key}`= ? ";
        }

        $where = " ( " . preg_replace("/^ *OR/", "", $where) . " ) ";

        return [$where, $binds];
    }


    private function toExpression(string $key, $expression, $val, array $binds)
    {
        switch ($expression)
        {
            case "in":
                // TODO bind
                $binds[] = "'" . implode("','", $val) . "'";
                $where = "`{$key}` IN ( ? )";
                break;
            case "!=":
            case "<>":
            case ">":
            case ">=":
            case "<":
            case "<=":
                $binds[] = $val;
                $where = "`{$key}` {$expression} ? ";
                break;
            case "like":
                $binds[] = $val;
                $where = "`{$key}` {$expression} %?% ";
                break;
            case "l-like":
                $binds[] = $val;
                $where = "`{$key}` {$expression} %? ";
                break;
            case "r-like":
                $binds[] = $val;
                $where = "`{$key}` {$expression} ?% ";
                break;

            default:
                throw new \Exception("不明[{$expression}]");
        }

        return [$where, $binds];

    }
}
