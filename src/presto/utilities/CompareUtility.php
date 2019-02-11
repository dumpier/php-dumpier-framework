<?php
namespace Presto\Utilities;

use Presto\Traits\Singletonable;

class CompareUtility
{
    use Singletonable;

    const EXPRESSION = [
        "between",
        "in", "not in",
        "like", "l-like", "r-like",
        "equal", "not", "differ", "large", "large-equal", "less", "less-equal",
        "=", "!=", "<>", ">", ">=", "<", "<=",
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
            case "between":
                return ($val >= $target_val[0]) && ($val <= $target_val[1]);

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
            case "not":
            case "differ":
                return !($val == $target_val);
            case ">":
            case "large":
                return $val > $target_val;
            case ">=":
            case "large-equal":
                return $val >= $target_val;
            case "<":
            case "less":
                return $val < $target_val;
            case "<=":
            case "less-equal":
                return $val <= $target_val;

            default:
                throw new \Exception("不明比較演算式:{$expression}, [val:{$val}][target:{$target_val}]");
        }
    }


    /**
     * 指定条件に該当するデータであるか
     * @param array $row
     * @param array $conditions
     * @param bool $isOr
     * @return boolean
     */
    public function isMatch(array $row, array $conditions, bool $isOr=FALSE)
    {
        foreach ($conditions as $key=>$val)
        {
            // グループ化した条件
            if(is_numeric($key))
            {
                if( $this->isMatch($row, $val) )
                {
                    if($isOr) return true; continue;
                }

                if ($isOr) continue; return false;
            }

            // OR検索
            if("or" === strtolower($key))
            {
                if( $this->isMatch($row, $val, TRUE) ) return true; continue;
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
                        if($isOr) return true; continue;
                    }

                    if ($isOr) continue; return false;
                }

                throw new \Exception("不明条件," . json_encode($conditions));
            }

            // プリミティブな比較
            if($row[$key] != $val)
            {
                if ($isOr) continue; return false;
            }
        }

        if ($isOr) return false; return true;
    }


}