<?php
namespace Presto\Core\Databases;

use Presto\Core\Traits\Singletonable;
use Presto\Core\Utilities\Expression;

class QueryToWhere
{
    use Singletonable;

    /**
     * 配列をSQL条件式に変換する
     * @param array $condition
     */
    public function convert(array $condition=[])
    {
        if(empty($condition))
        {
            return "";
        }

        list($where, $binds) = $this->where($condition);
        $where = "WHERE " . $this->clean($where);

        return [$where, $binds];
    }


    private function where(array $condition=[], array $binds=[])
    {
        $where = "";

        if(empty($condition))
        {
            return ["", []];
        }

        foreach ($condition as $key=>$val)
        {
            // 数字の場合は、グルーピング条件
            if(is_numeric($key))
            {
                list($sub_where, $binds) = $this->where($val, $binds);
                $where .= " AND ( {$sub_where} )";
                continue;
            }

            // ORグループの場合
            if('or' === strtolower($key))
            {
                list($sub_where, $binds) = $this->orWhere($val, $binds);
                $where .= " AND {$sub_where} ";
                continue;
            }

            // 配列の場合
            if(is_array($val))
            {
                $expression = key($val);
                if( expression()->is( $expression ) )
                {
                    // in, not in, >, >=, <, <=, <>, !=
                    list($sub_where, $binds) = $this->expression($key, $expression, $val[$expression], $binds);
                    $where .= " AND {$sub_where}";
                    continue;
                }

                // 配列の再帰処理
                list($sub_where, $binds) = $this->where($val, $binds);
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


    private function clean(string $where)
    {
        $where = preg_replace("/^ *AND /", "", $where);
        $where = preg_replace("/ {2,}/", " ", $where);
        $where = preg_replace("/\( */", "(", $where);
        $where = preg_replace("/ *\)/", ")", $where);
        $where = trim($where);

        return $where;
    }


    private function orWhere(array $condition, array $binds=[])
    {
        $where = "";

        foreach ( $condition as $key=>$val )
        {
            if(is_numeric($key))
            {
                list($sub_where, $binds) = $this->where($val, $binds);
                $where .= " OR ( {$sub_where} )";
                continue;
            }

            if(is_array($val))
            {
                if( expression()->is($val[0]) )
                {
                    // in, not in, >, >=, <, <=, <>, !=
                    list($sub_where, $binds) = $this->expression($key, $val[0], $val[1], $binds);
                    $where .= " OR {$sub_where}";
                    continue;
                }

                list($sub_where, $binds) = $this->where($condition, $binds);

                $where .= $sub_where;
                continue;
            }

            $binds[] = $val;
            $where .= " OR `{$key}`= ? ";
        }

        $where = " ( " . preg_replace("/^ *OR/", "", $where) . " ) ";

        return [$where, $binds];
    }


    private function expression(string $key, $expression, $val, array $binds)
    {
        switch ($expression)
        {
            case Expression::SIGN_EQUAL:
            case Expression::SIGN_DIFFER:
            case Expression::SIGN_LARGE:
            case Expression::SIGN_LARGE_OR_EQUAL:
            case Expression::SIGN_LESS:
            case Expression::SIGN_LESS_OR_EQUAL:
                $binds[] = $val;
                $where = "`{$key}` {$expression} ? ";
                break;

            case Expression::BETWEEN:
                $binds[] = "'" . implode("','", $val) . "'";
                $where = "`{$key}` BETWEEN ( ? AND ? )";
                break;

            case Expression::IN:
                // TODO bind
                $binds[] = "'" . implode("','", $val) . "'";
                $where = "`{$key}` IN ( ? )";
                break;
            case Expression::NOT_IN:
                // TODO bind
                $binds[] = "'" . implode("','", $val) . "'";
                $where = "`{$key}` NOT IN ( ? )";
                break;

            case Expression::LIKE:
                $binds[] = $val;
                $where = "`{$key}` LIKE %?% ";
                break;
            case Expression::L_LIKE:
                $binds[] = $val;
                $where = "`{$key}` LIKE %? ";
                break;
            case Expression::R_LIKE:
                $binds[] = $val;
                $where = "`{$key}` LIKE ?% ";
                break;

            default:
                throw new \Exception("不明[{$expression}]");
        }

        return [$where, $binds];

    }
}
