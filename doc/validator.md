# Validator
## 関数一覧
- validate(array $inputs, array $rules, bool $isOr=false)

```
$inputs = [];
$inputs["field_1"] = 1;
$inputs["field_2"] = 2;

$rules = [];
$rules["field_1"][] = ["require,integer"=>"必須 AND 数字"];
$rules["field_1"][] = ["betwwen(1,10)|in(11,21,31,41)"=>"1~10の間 or 11,21,31,41のいずれ"];
// 複数項目のAND結合条件
$rules[0]["field_1"] = ["require,equal(100)"];
$rules[0]["field_2"] = ["require,between(1~100)"];
// 複数項目のOR結合条件
$rules["or"]["field_1"] = ["require,equal(100)"];
$rules["or"]["field_2"] = ["require,between(1~100)"];

validator()->validate($inputs, $rules);
```

- cases($value, array $cases)

```
$cases = [];
$cases[] = ["require","message"];
$cases[] = ["numeric,between(1,100)","message"];

validator()->cases(11, "require,numeric|alpha,length(1,5)");

TODO......
$cases = [];
$cases[] = ["require","message"];
$cases["or"] = ["email,r-like(gmail.com)","message"];
$cases["or"] = ["numeric|alpha,length(1,100)","message"];

validator()->cases(11, "require,numeric|alpha,length(1,5)");
```


- case($value, string $string_expressions)

```
validator()->case(11, "require,numeric");
validator()->case(11, "require,numeric|alpha");
validator()->case(11, "require,numeric|alpha,length(1,5)");
```


- evalCaseExpression($value, string $case_expression)

```
validator()->evalCaseExpression(11, "length(1,5)");
validator()->evalCaseExpression(11, "between(1,15)");
```


- eval($value, $case, ...$expectations)

```
validator()->eval(11, "require");
validator()->eval(11, "integer");
validator()->eval(11, "between", 10, 20);
validator()->eval("test", "length", 4);
validator()->eval("test", "length", 4, 10);
```
  