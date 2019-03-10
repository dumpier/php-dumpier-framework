# PHP Presto Framework

https://scrutinizer-ci.com/g/dumpier/php-presto-framework/

```
軽量＆快速
```
<img src=https://i.imgur.com/fpeJwF8.png>

## Validate
https://github.com/dumpier/php-presto-framework/blob/master/doc/validator.md

## Database
```
// DB検索
$parameters = [];
$parameters["condition"]["last_login"][">="] = "2019-02-11";
$parameters["condition"]["or"]["level"]["in"] = [10,11,12];
$parameters["condition"]["or"]["rank"]["between"] = [3,4];

$players = dataabse("shard_01")->table("player")->paging($parameters);


// 配列検索
$condition = [];
$condition["A"]["in"] = [1,2,3,4,6];
$condition["or"]["B"]["in"] = [1,2];
$condition["or"]["C"] = 31;

collection($rows)->condition($condition);

// 配列の結合
$condition = [];
$condition["row1_key1"] = "row2_key1";
$condition["or"]["row1_key2"] = "row2_key2";
$condition["or"]["row1_key3"] = "row2_key3";
collection()->join($rows1, $rows2, $condition);
```

## Collection
- 連想配列の検索や結合を行う
```
$rows = [
    ["A"=>1, "B"=>1, "C"=>10, "D"=>4],
    ["A"=>2, "B"=>1, "C"=>11, "D"=>4],
    ["A"=>3, "B"=>2, "C"=>20, "D"=>4],
    ["A"=>4, "B"=>2, "C"=>21, "D"=>4],
    ["A"=>5, "B"=>3, "C"=>30, "D"=>4],
    ["A"=>6, "B"=>3, "C"=>31, "D"=>4],
];

$condition = [];
$condition["A"][">="] = 4;

$condition_1 = [];
$condition_1["A"][">="] = 3;
$condition_1["B"]["in"] = [1,2];

$condition_2 = [];
$condition_2["A"]["in"] = [1,2,3,4,6];
$condition_2["or"]["B"]["in"] = [1,2];
$condition_2["or"]["C"] = 31;

// 指定条件でフィルターリングする
collection($rows)->condition($condition);
collection($rows)->condition($condition_1);
collection($rows)->condition($condition_2);
```

```
// 複数の連想配列を結合する場合
$rows = [
    ["A"=>1, "B"=>1, "C"=>10, "D"=>4],
    ["A"=>2, "B"=>1, "C"=>11, "D"=>4],
    ["A"=>3, "B"=>2, "C"=>20, "D"=>4],
    ["A"=>4, "B"=>2, "C"=>21, "D"=>4],
    ["A"=>5, "B"=>3, "C"=>30, "D"=>4],
    ["A"=>6, "B"=>3, "C"=>31, "D"=>4],
];

$foreigns = [
    ["FA"=>1, "FB"=>1, "C"=>10, "D"=>4],
    ["FA"=>2, "FB"=>1, "C"=>11, "D"=>4],
    ["FA"=>3, "FB"=>2, "C"=>20, "D"=>4],
];

// 結合条件（結合時追加される項目名=>[現在配列の項目=>外部配列の項目,,,,,]）
$join = [
  "F_C"=>["A"=>"FA", "B"=>"FB"],
];

// 指定条件で結合する
collection()->mapping($rows, $foreigns, $join);
```

## Repository
- 複数のテーブルデータをJOINなしで結合する
```
// リポジトリの定義
class PlayerDeckRepository extends Repository
{
    protected $class = PlayerDeckModel::class;

    protected $relations = [
        // 結合する名前（任意の文字列 | リポジトリクラスの場合テーブル名に変換される）
        PlayerCharacterRepository::class=>[
            'type'=>Model::HAS_ONE,
            
            // 結合対象テーブルのリポジトリ
            "repository"=>PlayerCharacterRepository::class,

            // 0の場合は、PlayerCharacterの下位リレーションは自動でロードしない
            'recursion'=>0,

            // 現在テーブルが満たすべき条件
            'where'=>[
                "or"=>[
                    "player_character_id_1"=>['>'=> 0],
                    "player_character_id_2"=>['>'=> 0],
                    "player_character_id_3"=>['>'=> 0],
                    "player_character_id_4"=>['>'=> 0],
                    "player_character_id_5"=>['>'=> 0],
                    "player_character_id_6"=>['>'=> 0],
                ],
            ],

            // 結合条件（foregin_name=>[colurmn=>foreign_column]）
            'join'=>[
                "player_character_1"=>["player_character_id_1"=>"id"],
                "player_character_2"=>["player_character_id_2"=>"id"],
                "player_character_3"=>["player_character_id_3"=>"id"],
                "player_character_4"=>["player_character_id_4"=>"id"],
                "player_character_5"=>["player_character_id_5"=>"id"],
                "player_character_6"=>["player_character_id_6"=>"id"],
            ],
            
             // 外部テーブルが満たすべき条件
            'condition'=>[
                ["player_id"=>"player_id"],
            ],

        ],
    ];

}
```

```
// リポジトリのfind()で$recursion=1を渡すと下位リレーションをロードしてくれる
$parameters = [];
$parameters["condition"]["player_id"] = 1;
$this->playerDeck->find($parameters, $recursion=1);
```
