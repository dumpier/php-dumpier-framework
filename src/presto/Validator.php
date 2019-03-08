<?php
namespace Presto;

use Presto\Traits\Singletonable;

class Validator
{
    use Singletonable;

    const REGULAR = "regular";
    const REQUIRE = "require";
    const LENGTH = "length";
    const SIZE = "size";

    const NUMERIC = "numeric";
    const INTEGER = "integer";
    const DOUBLE = "double";
    const ALPHA = "alpha";
    const STRING = "string";

    const SIGN = "sign";

    const EMAIL = "email";
    const URL = "url";
    const DOMAIN = "domain";

    const TELEPHONE = "telephone";
    const ZIP_CODE = "zip-code";
    const COUNTRY_CODE = "country-code";



    /**
     * サポートするルール一覧
     * @var array
     */
    protected $providers = [
        // 必須
        self::REQUIRE=>["message"=>"必須", ],
        // 正規表現、例）regular:/aaaaa/
        self::REGULAR=>["message"=>"正規表現",
            "parameters"=>true],

        self::LENGTH =>["message"=>"文字数", "parameters"=>true],
        self::SIZE =>["message"=>"サイズ", ],

        self::NUMERIC=>["message"=>"数字", ],
        self::INTEGER=>["message"=>"整数", ],
        self::DOUBLE=>["message"=>"小数", ],
        self::ALPHA=>["message"=>"半角英文字", ],
        self::STRING=>["message"=>"文字列???", ],
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


    /**
     * ケースに振り分ける
     * @param mixed $input
     * @param array $cases
     *      - 'require'
     *      - 'require,integer'
     *      -
     *          'require, integer',
     *          'between(1,5)'=>'1から5の間'
     *
     */
    public function switch($input, array $cases)
    {
        foreach ($cases as $somekey=>$someting)
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
            $cases = pregular()->all("//", $case_string);

            // TODO TODO TODO TODO TODO TODO TODO
            foreach ($cases as $case)
            {

            }
        }
    }


    /**
     * ケースの振り分け
     * @param mixed $input
     * @param string $case_string
     *      - require <=> 必須
     *      - require,numeric <=> 必須 and 数字
     *      - require,numeric,between(1~100) <=> 必須 and 数字 and 1~100の間
     *      - require|numeric <=> 必須 or 数字
     *      - require,numeric|alpha  <=> 必須 and (数字 or 英文字)
     *      - require,numeric|alpha,length(3~5)  <=> 必須 and (数字 or 英文字) and 文字数が3~5間
     *      - require,numeric,large(5)  <=> 必須 and 数字 and 5以上
     * @param mixed $expectations
     * @param string $message
     * @return boolean[]|string[]
     */
    public function cases($input, array $cases, $expectations=null, $message="")
    {

    }



    public function case($input, string $string_expressions)
    {
        // カンマを含めない場合、ANDで評価する必要がなく、ORで評価する必要があるか確認する
        if(! preg_match("/,/", $string_expressions))
        {
            return $this->evalExpressionInOr($input, $string_expressions);
        }

        // カンマで区切り、ANDで評価する
        $expressions = explode(",", $string_expressions);

        foreach ($expressions as $expression)
        {
            if( !$this->evalExpressionInOr($input, $expression) )
            {
                return false;
            }
        }

        return true;
    }


    /**
     * OR条件での評価
     * @param mixed $input
     * @param string $string_expressions
     * @return boolean
     */
    public function evalExpressionInOr($input, string $string_expressions)
    {
        if(! preg_match("/\|/", $string_expressions))
        {
            return $this->evalExpression($input, $string_expressions);
        }

        $expressions = explode("|", $string_expressions);

        foreach ($expressions as $expression)
        {
            if($this->evalExpression($input, $expression))
            {
                return true;
            }
        }

        return false;
    }


    /**
     * ケース文を評価する
     * @param mixed $input
     * @param string $expression
     * @return mixed|boolean
     */
    public function evalExpression($input, string $expression)
    {
        if(! preg_match("/.+\(.+\)/", $expression))
        {
            return $this->eval($input, $expression);
        }

        // パラメータ付きを評価する
        // 例）equal(1), between(1~5)
        $case = preg_replace("/(.+)\(.+\)/", "$1", $expression);

        // 期待値
        $expectation_string = preg_replace("/.+\((.+)\)/", "$1", $expression);
        $expectations = explode("~", $expectation_string);

        // 配列にする
        $parameters = [$input, $case];
        $parameters = array_merge($parameters, $expectations);
        return call_user_func_array([$this, "eval"], $parameters);
        // return $this->eval($input, $case, ...$expectations);
    }


    /**
     * 各種ケースの評価
     * @param mixed $input 入力値
     * @param string $case ケース
     * @param mixed ...$expectations 期待値
     * @throws \Exception
     * @return boolean
     */
    public function eval($input, string $case, ...$expectations)
    {
        var_dump(func_get_args());
        // 必須チェックの場合
        if($case===self::REQUIRE)
        {
            return $input !== null && $input !== "";
        }

        // 比較演算の場合
        if( expression()->is($case) )
        {
            return expression()->compare($input, $case, ...$expectations);
        }

        // その他
        switch ($case)
        {
            case self::REGULAR:
                // 正規表現、例）regular:/aaaaa/
                return preg_match($expectations, $input) > 0;

            case self::NUMERIC:
            case self::INTEGER:
            case self::DOUBLE:
                return is_numeric($input);

            case self::ALPHA:
                return preg_match("/[a-zA-Z]/", $input) > 0;
            case self::SIGN:
                return preg_match("/".preg_quote("\"!#$%&'()-=^~\|@{}[];:<>,./?\_")."/", $input) > 0;

            case self::EMAIL:
                return preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $input) > 0;
            case self::URL:
                return preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $input) > 0;
            case self::DOMAIN:
                return preg_match('/^([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+)/i', $input) > 0;

            case self::ZIP_CODE:
                return preg_match("/^[0-9]{3}-[0-9]{4}$/", $input) > 0;
            case self::TELEPHONE:
                return true; // TODO
            case self::COUNTRY_CODE:
                return true; // TODO

            case self::LENGTH:
            case self::SIZE:
                return expression()->compare(strlen($input), Expression::BETWEEN, ...$expectations);

            default:
                throw new \Exception("不明Validate[case:{$case}]");
        }
    }


    /**
     * 指定ルールのデフォルトメッセージを返す
     * @param string $case_name
     * @return string|string
     */
    private function getDefaultMessage(string $case_name, array $cases=[])
    {
        if(empty($this->providers[$case_name]["message"]))
        {
            return "エラー";
        }

        return $this->providers[$case_name]["message"];
    }


}