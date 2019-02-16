<?php
namespace Presto\Services\Codes;

use Presto\Mvc\Model\Entity\Table;

class GenerateModelService extends \Presto\Service
{
    /**
     * Modelテンプレートの生成
     * @param Table $table
     * @return string[]|string[][]
     */
    public function generatePHPDoc(Table $table)
    {
        // DB名、ネームスペース、クラス名、ファイルパース
        list($dbname, $namespace, $classname, $file) = $this->getPHPDocInfo($table);
        list(, $model_namespace, $model_classname, ) = $this->getDaoInfo($table);

        $codes = [];
        $codes[] = "namespace {$namespace};";
        $codes[] = "";
        $codes[] = "/**";

        // -------------------------------------
        // テーブル項目一覧
        // -------------------------------------
        foreach ($table->columns as $column)
        {
            $type = $column->getType();
            $codes[] = " * @property {$type} \${$column->Field} {$column->Comment}";
        }

        $codes[] = " */";
        // -------------------------------------

        $codes[] = "trait {$classname}";
        $codes[] = "{";

        // -------------------------------------
        // テーブル定義の出力
        // -------------------------------------
        $codes[] = "    public static \$COLUMNS = [";
        foreach ($table->columns as $column)
        {
            $type = $column->getType();
            $codes[] = "        '{$column->Field}' =>['type'=> '{$type}', 'original_type' => '{$column->Type}', 'name' => '{$column->Comment}', ],";
        }
        $codes[] = "    ];";
        // -------------------------------------

        // -------------------------------------
        // モデル新規作成
        // -------------------------------------
        $codes[] = "";
        $codes[] = "    /**";
        $codes[] = "     * 新規データ作成";
        $codes[] = "     * @param array \$row";
        $codes[] = "     * @return \\{$model_namespace}\\{$model_classname}";
        $codes[] = "     */";
        $codes[] = "    public static function add(array \$row=[])";
        $codes[] = "    {";
        $codes[] = "        \$self = new static;";
        $codes[] = "";
        $codes[] = "        // 入力値のチェック";
        $codes[] = "        \$self->validate(\$row);";
        $codes[] = "";
        $codes[] = "        // 代入処理";
        $codes[] = "        \$self->initialize(\$row);";
        $codes[] = "";
        $codes[] = "        \$self->save();";
        $codes[] = "        return \$self;";
        $codes[] = "    }";
        $codes[] = "";
        // -------------------------------------

        $codes[] = "}";

        return [$file, $codes];
    }


    /**
     * プロパティの生成
     * @param Table $table
     * @return string[]|string[][]
     */
    public function generatePropertyCode(Table $table)
    {
        // DB名、ネームスペースとクラス名
        list($dbname, $namespace, $classname, $file) = $this->getPropertyInfo($table);
        // ファイルパース
        $file = "Templates/Properties/{$dbname}/{$classname}.php";

        $codes = [];
        $codes[] = "namespace {$namespace};";
        $codes[] = "";
        $codes[] = "/**";
        $codes[] = " */";
        $codes[] = "class {$classname}";
        $codes[] = "{";

        foreach ($table->columns as $column)
        {
            $codes[] = "    public \${$column->Field} = " . $column->getDefaultValueExpression() . ";";
        }

        $codes[] = "}";


        return [$file, $codes];
    }


    /**
     * Daoの生成
     * @param Table $table
     * @return string[]|string[][]
     */
    public function generateDaoCode(Table $table)
    {
        // PHPDocのDB名、ネームスペースとクラス名
        list(, $phpdoc_namespace, $phpdoc_classname,) = $this->getPHPDocInfo($table);
        // DB名、ネームスペースとクラス名
        list($dbname, $namespace, $classname, $file) = $this->getDaoInfo($table);

        $codes = [];
        $codes[] = "namespace {$namespace};";
        $codes[] = "";
        $codes[] = "use {$phpdoc_namespace}\\{$phpdoc_classname};";
        $codes[] = "";
        $codes[] = "/**";
        $codes[] = " * {$table->name}";
        $codes[] = " */";
        $codes[] = "class {$classname} extends \\App\\Models\\Daos\\Base{$dbname}Model";
        $codes[] = "{";
        $codes[] = "    use {$phpdoc_classname};";
        $codes[] = "";
        $codes[] = "    protected \$table = '{$table->name}';";
        $codes[] = "}";

        return [$file, $codes];
    }


    /**
     * Repositoryの生成
     * @param Table $table
     * @return string[]|string[][]
     */
    public function generateRepositoryCode(Table $table)
    {
        // DaoのDB名、ネームスペースとクラス名
        list(, $dao_namespace, $dao_classname, ) = $this->getDaoInfo($table);
        // DB名、ネームスペースとクラス名
        list($dbname, $namespace, $classname, $file) = $this->getRepositoryInfo($table);

        $codes = [];
        $codes[] = "namespace {$namespace};";
        $codes[] = "";
        $codes[] = "use App\\Models\\Repositories\\Base{$dbname}Repository;";
        $codes[] = "use {$dao_namespace}\\{$dao_classname};";
        $codes[] = "";
        $codes[] = "/**";
        $codes[] = " * {$table->name}";
        $codes[] = " */";
        $codes[] = "class {$classname} extends Base{$dbname}Repository";
        $codes[] = "{";
        $codes[] = "    protected \$model = {$dao_classname}::class;";
        $codes[] = "}";

        return [$file, $codes];
    }


    private function getPHPDocInfo(Table $table)
    {
        $dbname = stringer()->toPascal($table->connection);
        $classname = stringer()->toPascal($table->name)."DocTrait";
        $namespace = "App\\Models\\Templates\\Docs\\{$dbname}";
        $file = "Templates/Docs/{$dbname}/{$classname}.php";

        return [$dbname, $namespace, $classname, $file];
    }

    private function getPropertyInfo(Table $table)
    {
        $dbname = stringer()->toPascal($table->connection);
        $classname = stringer()->toPascal($table->name)."Property";
        $namespace = "App\\Models\\Templates\\Properties\\{$dbname}";
        $file = "Templates/Properties/{$dbname}/{$classname}.php";

        return [$dbname, $namespace, $classname, $file];
    }

    private function getDaoInfo(Table $table)
    {
        $dbname = stringer()->toPascal($table->connection);
        $classname = stringer()->toPascal($table->name)."Model";
        $namespace = "App\\Models\\Daos\\{$dbname}";
        $file = "Daos/{$dbname}/{$classname}.php";

        return [$dbname, $namespace, $classname, $file];
    }

    private function getRepositoryInfo(Table $table)
    {
        $dbname = stringer()->toPascal($table->connection);
        $classname = stringer()->toPascal($table->name)."Repository";
        $namespace = "App\\Models\\Repositories\\{$dbname}";
        $file = "Repositories/{$dbname}/{$classname}.php";

        return [$dbname, $namespace, $classname, $file];
    }

}