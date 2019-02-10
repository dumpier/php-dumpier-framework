<?php
namespace Presto\Utilities;

use Presto\Traits\Singletonable;

class CompareUtility
{
    use Singletonable;

    const EXPRESSION = [
        "=", "equal",
        "!=", "<>", "notEqual",
        ">", ">=",
        "<", "<=",
        "in", "not in",
        "like", "l-like", "r-like"
    ];


    /**
     * 比較演算
     * @param mixed $val
     * @param string $expression
     * @param mixed $target_val
     * @throws \Exception
     * @return boolean
     */
    public function compare($val, $expression, $target_val)
    {
        switch ($expression)
        {
            case "in":
                return in_array($val, $target_val, true);

            case "not in":
                return ! in_array($val, $target_val, true);

            case "like":
                return preg_match("/{$val}/", $target_val);
            case "l-like":
                return preg_match("/^{$val}/", $target_val);
            case "r-like":
                return preg_match("/{$val}$/", $target_val);

            case "=":
            case "equal":
                return ($val == $target_val);

            case "!=":
            case "<>":
                return !($val == $target_val);

            case ">":
                return $val > $target_val;

            case ">=":
                return $val >= $target_val;

            case "<":
                return $val < $target_val;

            case "<=":
                return $val <= $target_val;

            default:
                throw new \Exception("不明比較演算式:{$expression}, [val:{$val}][target:{$target_val}]");
        }
    }


    /**
     * 指定条件に該当するデータであるか
     * @param array $row
     * @param array $conditions
     * @param bool $isAnd
     * @return boolean
     */
    public function isMatch(array $row, array $conditions, bool $isAnd=true)
    {
        foreach ($conditions as $key=>$val)
        {
            // グループ化した条件
            if(is_numeric($key))
            {
                if( $this->isMatch($row, $val) )
                {
                    if($isAnd) continue; return true;
                }

                if ($isAnd) return false; continue;
            }

            // OR検索
            if("or" === strtolower($key))
            {
                if( $this->isMatch($row, $val, FALSE) ) return true; continue;
            }

            // 配列の場合
            if( is_array($val) )
            {
                // 演算式
                $expression = key($val);
                if( in_array($expression, self::EXPRESSION) )
                {
                    if($this->compare($row[$key], $expression, $val[$expression]))
                    {
                        if($isAnd) continue; return true;
                    }

                    if ($isAnd) return false; continue;
                }

                throw new \Exception("不明条件," . json_encode($conditions));
            }

            // プリミティブな比較
            if($row[$key] != $val)
            {
                if ($isAnd) return false; continue;
            }
        }

        if ($isAnd) return true; return false;
    }


}