<?php
namespace Presto;

use Presto\Traits\Instanceable;

class Validator
{
    use Instanceable;

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
        self::REGULAR=>["message"=>"正規表現", "parameters"=>true],

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


    /**
     * バリデーター
     * TODO OR条件、グルピング条件未完成
     * @param array $inputs
     * @param array $rules
     * @param bool $isOr
     * @return boolean[]|array[][]
     */
    public function validate(array $inputs, array $rules, bool $isOr=false)
    {
        $result = true;
        $messages = [];

        foreach ($rules as $field=>$rule_case)
        {
            if("or" === strtolower($field))
            {
                // OR条件 TODO 未完成
                continue;
            }

            if(is_numeric($field))
            {
                // グルーピング条件 TODO 未完成
                continue;
            }

            $value = isset($inputs[$field]) ? $inputs[$field] : "";
            list($return, $msgs) = $this->case($value, $rule_case);

            if(! $return )
            {
                $messages[] = array_merge($messages, $msgs);
            }
        }

        return [$result, $messages];
    }


    /**
     * 1つの値に対して複数のケース演算を評価する
     * TODO ORグルピング
     * @param mixed $value
     * @param array $cases
     * @param string $message
     * @return boolean[]|string[]
     */
    public function cases($value, array $cases)
    {
        $result = true;
        $messages = [];

        foreach ($cases as $case=>$message)
        {
            if( $this->case($value, $case) )
            {
                continue;
            }

            $messages[] = $message;
            $result = false;
        }

        return [$result, $messages];
    }


    /**
     * カンマをAND、バーティカルバーをOR結合条件としてケース演算式を評価する
     * @param mixed $value 入力値
     * @param string $string_expressions
     *      例）require,numeric <=> 必須 and 数字
     *      例）require,alpha|numeric <=> 必須 and (数字 or 英文字)
     *      例）require,numeric,between(1,10) <=> 必須 and 数字 and 1~10の間
     *      例）require,alpha|numeric,length(5~) <=> 必須 and (数字 or 英文字) and 桁数が5以上
     *      例）require,numeric,large(5)  <=> 必須 and 数字 and 5以上
     * @return boolean
     */
    public function case($value, string $string_expressions)
    {
        // カンマを含めない場合、ANDで評価する必要がなく、ORで評価する必要があるか確認する
        if(! preg_match("/,/", $string_expressions))
        {
            return $this->evalCaseExpressionInOr($value, $string_expressions);
        }

        // カンマで区切り、ANDで評価する
        $expressions = explode(",", $string_expressions);

        foreach ($expressions as $expression)
        {
            if( !$this->evalCaseExpressionInOr($value, $expression) )
            {
                return false;
            }
        }

        return true;
    }


    /**
     * バーティカルバー区切りをOR条件での評価する
     * @param mixed $value
     * @param string $string_case_expressions
     * @return boolean
     */
    private function evalCaseExpressionInOr($value, string $string_case_expressions)
    {
        if(! preg_match("/\|/", $string_case_expressions))
        {
            return $this->evalCaseExpression($value, $string_case_expressions);
        }

        // バーティカルバーで分割する
        $expressions = explode("|", $string_case_expressions);

        foreach ($expressions as $expression)
        {
            if($this->evalCaseExpression($value, $expression))
            {
                return true;
            }
        }

        return false;
    }


    /**
     * ケース演算文を評価する
     * @param mixed $value
     * @param string $case_expression
     * @return mixed|boolean
     */
    public function evalCaseExpression($value, string $case_expression)
    {
        // パラメータつきの演算でない場合
        if(! preg_match("/.+\(.+\)/", $case_expression))
        {
            return $this->eval($value, $case_expression);
        }

        // パラメータ付きを評価する
        // 例）equal(1), between(1~5)
        $case = preg_replace("/(.+)\(.+\)/", "$1", $case_expression);

        // 期待値
        $expectation_string = preg_replace("/.+\((.+)\)/", "$1", $case_expression);
        $expectations = explode("~", $expectation_string);

        // 配列にして$this->eval()を呼び出す
        $parameters = [$value, $case];
        $parameters = array_merge($parameters, $expectations);
        return call_user_func_array([$this, "eval"], $parameters);
        // return $this->eval($value, $case, ...$expectations);
    }


    /**
     * 各種ケースを評価する
     * @param mixed $value 入力値
     * @param string $case ケース
     * @param mixed ...$expectations 期待値
     * @throws \Exception
     * @return boolean
     */
    public function eval($value, string $case, ...$expectations)
    {
        // 必須チェックの場合
        if($case===self::REQUIRE)
        {
            return $value !== null && $value !== "";
        }

        // 比較演算の場合
        if( expression()->is($case) )
        {
            return expression()->compare($value, $case, ...$expectations);
        }

        // その他
        switch ($case)
        {
            case self::REGULAR:
                // 正規表現、例）regular:/aaaaa/ TODO 未完成
                return preg_match($expectations, $value) > 0;

            case self::NUMERIC:
            case self::INTEGER:
            case self::DOUBLE:
                return is_numeric($value);

            case self::ALPHA:
                return preg_match("/[a-zA-Z]/", $value) > 0;
            case self::SIGN:
                return preg_match("/".preg_quote("\"!#$%&'()-=^~\|@{}[];:<>,./?\_")."/", $value) > 0;

            case self::EMAIL:
                return preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $value) > 0;
            case self::URL:
                return preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $value) > 0;
            case self::DOMAIN:
                return preg_match('/^([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+)/i', $value) > 0;

            case self::ZIP_CODE:
                return preg_match("/^[0-9]{3}-[0-9]{4}$/", $value) > 0;
            case self::TELEPHONE:
                return true; // TODO
            case self::COUNTRY_CODE:
                return true; // TODO

            case self::LENGTH:
            case self::SIZE:
                return expression()->compare(strlen($value), Expression::BETWEEN, ...$expectations);

            default:
                throw new \Exception("不明Validate[case:{$case}]");
        }
    }


    /**
     * 指定ルールのデフォルトメッセージを返す
     * @param string $case_name
     * @return string|string
     */
    private function getDefaultMessage(string $case_name)
    {
        if(empty($this->providers[$case_name]["message"]))
        {
            return "エラー";
        }

        return $this->providers[$case_name]["message"];
    }


}