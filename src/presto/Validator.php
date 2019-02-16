<?php
namespace Presto;

use Presto\Traits\Singletonable;
use Presto\Express;

class Validator
{
    use Singletonable;

    const REGULAR = "regular";
    const REQUIRE = "require";

    const INTEGER = "integer";
    const DOUBLE = "double";
    const ALPHA = "alpha";
    const SIGN = "sign";

    const EMAIL = "email";
    const URL = "url";
    const DOMAIN = "domain";

    const TELEPHONE = "telephone";
    const ZIP_CODE = "zip-code";
    const COUNTRY_CODE = "country-code";

    const LENGTH = "length";
    const LENGTH_MIN = "length-min";
    const LENGTH_MAX = "length-max";
    const LENGTH_BETWEEN = "length-between";

    /**
     * サポートするルール一覧
     * TODO 外部から取り込めるようにする、ルール毎に呼び出し関数とデフォルトメッセージを用意する
     * @var array
     */
    protected $providers = [
        // 必須
        self::REQUIRE=>["message"=>"必須", ],

        // 正規表現、例）regular:/aaaaa/
        self::REGULAR=>["message"=>"正規表現", ],

        self::INTEGER=>["message"=>"整数", ],
        self::DOUBLE=>["message"=>"小数", ],
        self::ALPHA=>["message"=>"半角英文字", ],
        self::SIGN=>["message"=>"符号", ],

        self::EMAIL=>["message"=>"Email", ],
        self::URL=>["message"=>"リンク", ],
        self::DOMAIN=>["message"=>"ドメイン", ],

        self::TELEPHONE=>["message"=>"電話番号", ],
        self::ZIP_CODE=>["message"=>"郵便番号", ],
        self::COUNTRY_CODE=>["message"=>"国番号", ],
    ];

    public function provider(string $name, array $config)
    {
        $this->providers[$name] = $config;
    }


    /**
     *
     * @param array $rules
     *      // rules =>[key=>rule,,,]
     *      $rules = [
     *          // rule=>[key=>case,case,case]
     *          "is_valid"=>"require,bool,equal(true)",
     *          // rule=>[...]
     *          "price"=>[
     *              // case=>message
     *              "require"=>"is required!",
     *              // case|case=>message
     *              "double|integer"=>"is require numeric !",
     *              // case
     *              "between(1,100)",
     *          ],
     *
     *          // rules=>[rule, rule]
     *          "or"=>[
     *              "item_type"=>"require,integer,length(6)",
     *              "item_id"=>"require,integer",
     *          ],
     *      ];
     * @param array $row
     * @param bool $isOr
     * @return []
     *
     * TODO TODO TODO TODO TODO TODO TODO TODO 未完成
     */
    public function validates(array $rules, array $row, bool $isOr=false)
    {
        $messages = [];

        foreach ($rules as $field=>$rule)
        {
            if("or" === strtolower($field))
            {
                list($or_result, $orMessages) = $this->validate($rule, $row, TRUE);
            }

            $val = isset($row[$field]) ? $row[$field] : null;
            list($result, $message) = $this->validate($val, $rule);

            if( $result )
            {
                if($isOr) return [true, ""]; continue;
            }

            $messages[] = $message;
        }

        return [$result, $messages];
    }

    // TODO TODO TODO TODO TODO TODO TODO
    public function validate($val, array $rule)
    {
        foreach ($rule as $somekey=>$someting)
        {
            if(is_numeric($somekey))
            {
                $case_string = $someting;
                $message = "";
            }
            else
            {
                $case_string = $somekey;
                $message = $someting;
            }

            // TODO TODO TODO TODO TODO TODO TODO
            // TODO カンマーとバーティカルバーで配列に分割
            $cases = preg()->all("//", $case_string);

            // TODO TODO TODO TODO TODO TODO TODO
            foreach ($cases as $case)
            {

            }
        }
    }




    /**
     * 振り分け
     * @param array $row
     * @param string $key
     * @param string $case
     * @param mixed $case_value
     * @param string $message
     * @return boolean
     */
    public function switch($val,  string $case, $case_value=null, $message="")
    {
        $result = true;

        if($case===self::REQUIRE)
        {
            // 必須チェックの場合
            $result = $val !== null && $val !== "";
        }
        elseif($val==null || $val=="")
        {
            // 必須チェック以外の場合、入力がなければチェックを通す
            $result = true;
        }
        else
        {
            // その他のケース
            $result = $this->case($val, $case, $case_value);
        }

        $message = empty($message) ? $this->getDefaultMessage($case) : $message;
        return [$result, $message];
    }


    /**
     * 各種チェックケース
     * @param mixed $val
     * @param mixed $rule
     * @param mixed $rule_value
     * @throws \Exception
     * @return boolean
     */
    public function case($val,  $case, $case_value=null)
    {
        if(in_array($case, Express::LIST))
        {
            return express()->compare($val, $case, $case_value);
        }

        switch ($case)
        {
            case self::REGULAR:
                // 正規表現、例）regular:/aaaaa/
                return preg_match($case_value, $val) > 0;

            case self::INTEGER:
            case self::DOUBLE:
                return is_numeric($val);

            case self::ALPHA:
                return preg_match("/[a-zA-Z]/", $val) > 0;
            case self::SIGN:
                return preg_match("/".preg_quote("\"!#$%&'()-=^~\|@{}[];:<>,./?\_")."/", $val) > 0;

            case self::EMAIL:
                return preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $val) > 0;
            case self::URL:
                return preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $val) > 0;
            case self::DOMAIN:
                return preg_match('/^([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+)/i', $val) > 0;

            case self::ZIP_CODE:
                return preg_match("/^[0-9]{3}-[0-9]{4}$/", $val) > 0;
            case self::TELEPHONE:
                return true; // TODO
            case self::COUNTRY_CODE:
                return true; // TODO

            case self::LENGTH:
                return express()->compare(strlen($val), self::EQUAL, $case_value);
            case self::LENGTH_MIN:
                return express()->compare(strlen($val), self::LARGE_OR_EQUAL, $case_value);
            case self::LENGTH_MAX:
                return express()->compare(strlen($val), self::LESS_OR_EQUAL, $case_value);
            case self::LENGTH_BETWEEN:
                return express()->compare(strlen($val), self::BETWEEN, $case_value);

            default:
                throw new \Exception("不明Validate[rule:{$case}]");
        }
    }


    /**
     * 指定ルールのデフォルトメッセージを返す
     * @param string $rule_name
     * @return string|string
     */
    public function getDefaultMessage(string $rule_name)
    {
        if(empty($this->providers[$rule_name]["message"]))
        {
            return "";
        }

        return $this->providers[$rule_name]["message"];
    }


}