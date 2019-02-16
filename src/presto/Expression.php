<?php
namespace Presto;

use Presto\Traits\Singletonable;

class Expression
{
    use Singletonable;

    // -----------------------------------
    // 演算式
    // -----------------------------------
    const SIGN_EQUAL = "=";
    const SIGN_NOT = "!=";
    const SIGN_DIFFER = "<>";
    const SIGN_LARGE = ">";
    const SIGN_LARGE_OR_EQUAL = ">=";
    const SIGN_LESS = "<";
    const SIGN_LESS_OR_EQUAL = "<=";

    const BETWEEN = "between";
    const IN = "in";
    const NOT_IN = "not in";

    const LIKE = "like";
    const L_LIKE = "l-like";
    const R_LIKE = "r-like";

    const EQUAL = "equal";
    const NOT = "not";
    const DIFFER = "differ";

    const LARGE = "large";
    const LARGE_OR_EQUAL = "large-equal";
    const LESS = "less";
    const LESS_OR_EQUAL = "less-equal";


    const LIST = [
        self::SIGN_EQUAL=>["message"=>"等しい", ],
        self::SIGN_NOT=>["message"=>"異なる", ],
        self::SIGN_DIFFER=>["message"=>"異なる", ],
        self::SIGN_LARGE=>["message"=>"大きい", ],
        self::SIGN_LARGE_OR_EQUAL=>["message"=>"以上", ],
        self::SIGN_LESS=>["message"=>"小さい", ],
        self::SIGN_LESS_OR_EQUAL=>["message"=>"以下", ],


        self::BETWEEN=>["message"=>"指定範囲内", ],
        self::IN=>["message"=>"指定一覧内", ],
        self::NOT_IN=>["message"=>"指定一覧以外", ],

        self::LIKE=>["message"=>"類似", ],
        self::L_LIKE=>["message"=>"左寄り類似", ],
        self::R_LIKE=>["message"=>"右寄り類似", ],

        self::EQUAL=>["message"=>"等しい", ],
        self::NOT=>["message"=>"異なる", ],
        self::DIFFER=>["message"=>"異なる", ],
        self::LARGE=>["message"=>"大きい", ],
        self::LARGE_OR_EQUAL=>["message"=>"以上", ],
        self::LESS=>["message"=>"小さい", ],
        self::LESS_OR_EQUAL=>["message"=>"以下", ],
    ];


    /**
     * 有効な演算表現であるか
     * @param string $expression
     * @return boolean
     */
    public function is(string $expression)
    {
        if(in_array($expression, array_keys(self::LIST), TRUE))
        {
            return true;
        }

        false;
    }


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
            case self::BETWEEN:
                return ($val >= $target_val[0]) && ($val <= $target_val[1]);

            case self::IN:
                return in_array($val, $target_val, true);
            case self::NOT_IN:
                return ! in_array($val, $target_val, true);

            case self::LIKE:
                return preg_match("/{$val}/", $target_val);
            case self::L_LIKE:
                return preg_match("/^{$val}/", $target_val);
            case self::R_LIKE:
                return preg_match("/{$val}$/", $target_val);

            case self::SIGN_EQUAL:
            case self::EQUAL:
                return ($val == $target_val);

            case self::SIGN_NOT:
            case self::SIGN_DIFFER:
            case self::NOT:
            case self::DIFFER:
                return !($val == $target_val);

            case self::SIGN_LARGE:
            case self::LARGE:
                return $val > $target_val;

            case self::SIGN_LARGE_OR_EQUAL:
            case self::LARGE_OR_EQUAL:
                return $val >= $target_val;

            case self::SIGN_LESS:
            case self::LESS:
                return $val < $target_val;

            case self::SIGN_LESS_OR_EQUAL:
            case self::LESS_OR_EQUAL:
                return $val <= $target_val;

            default:
                throw new \Exception("不明比較演算式:{$expression}, [val:{$val}][target:{$target_val}]");
        }
    }


    /**
     * 指定条件に該当するデータであるか
     * @param array $row
     * @param array $condition
     * @param bool $isOr
     * @return boolean
     */
    public function isMatch(array $row, array $condition, bool $isOr=FALSE)
    {
        foreach ($condition as $key=>$val)
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
                if( in_array($expression, array_keys(self::LIST)) )
                {
                    if($this->compare($row[$key], $expression, $val[$expression]))
                    {
                        if($isOr) return true; continue;
                    }

                    if ($isOr) continue; return false;
                }

                throw new \Exception("不明条件," . json_encode($condition));
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