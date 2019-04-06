<?php
namespace Presto\Core\Services;

use Presto\Core\Databases\Model\Entity\Table;
use Presto\Core\Utilities\Stringer;

class GenerateModelService extends \Presto\Core\Service
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
            // $type = $column->getType();
            // $codes[] = " * @property {$type} \${$column->Field} {$column->Comment}";
        }

        $codes[] = " */";
        // -------------------------------------

        $codes[] = "trait {$classname}";
        $codes[] = "{";

        // -------------------------------------
        // テーブル定義の出力
        // -------------------------------------
        // $codes[] = "    /** テーブル名 */";
        // $codes[] = "    protected \$table = \"{$table->name}\";";
        // $codes[] = "";
        $codes[] = "    /** 項目一覧 */";
        $codes[] = "    protected \$properties = [";
        foreach ($table->columns as $column)
        {
            $type = $column->getType();
            $codes[] = "        \"{$column->Field}\",";
            // $codes[] = "        '{$column->Field}' =>['type'=> '{$type}', 'original_type' => '{$column->Type}', 'comment' => '{$column->Comment}', ],";
        }
        $codes[] = "    ];";
        // -------------------------------------

        $codes[] = "";
        $codes[] = "";

        // 項目一覧
        foreach ($table->columns as $column)
        {
            $type = $column->getType();
            $codes[] = "    /** @var {$type} {$column->Comment} */";
            $codes[] = "    public \${$column->Field} = " . $column->getDefaultValueExpression() . ";";
            $codes[] = "";
        }

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
        $codes[] = "    protected \$table = \"{$table->name}\";";
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
        $codes[] = "use Presto\Core\Databases\Model\Repository;";
        $codes[] = "use {$dao_namespace}\\{$dao_classname};";
        $codes[] = "";
        $codes[] = "/**";
        $codes[] = " * {$table->name}";
        $codes[] = " */";
        $codes[] = "class {$classname} extends Repository";
        $codes[] = "{";
        $codes[] = "    protected \$class = {$dao_classname}::class;";
        $codes[] = "}";

        return [$file, $codes];
    }


    private function getPHPDocInfo(Table $table)
    {
        list($connection, $connection_upper) = $this->getConnectionName($table);
        $classname = Stringer::instance()->toPascal($table->name)."DocTrait";
        $namespace = "App\\Models\\Templates\\Docs\\{$connection_upper}";

        // フォルダ名は全部小文字
        $file = "templates/docs/{$connection}/{$classname}.php";

        return [$connection_upper, $namespace, $classname, $file];
    }

    private function getPropertyInfo(Table $table)
    {
        list($connection, $connection_upper) = $this->getConnectionName($table);
        $classname = Stringer::instance()->toPascal($table->name)."Property";
        $namespace = "App\\Models\\Templates\\Properties\\{$connection_upper}";

        // フォルダ名は全部小文字
        $file = "templates/properties/{$connection}/{$classname}.php";

        return [$connection_upper, $namespace, $classname, $file];
    }

    private function getDaoInfo(Table $table)
    {
        list($connection, $connection_upper) = $this->getConnectionName($table);
        $classname = Stringer::instance()->toPascal($table->name)."Model";
        $namespace = "App\\Models\\Daos\\{$connection_upper}";

        // フォルダ名は全部小文字
        $file = "daos/{$connection}/{$classname}.php";

        return [$connection_upper, $namespace, $classname, $file];
    }

    private function getRepositoryInfo(Table $table)
    {
        list($connection, $connection_upper) = $this->getConnectionName($table);
        $classname = Stringer::instance()->toPascal($table->name)."Repository";
        $namespace = "App\\Models\\Repositories\\{$connection_upper}";

        // フォルダ名は全部小文字
        $file = "repositories/{$connection}/{$classname}.php";

        return [$connection_upper, $namespace, $classname, $file];
    }


    private function getConnectionName(Table $table)
    {
        $connection = preg_replace("/_[0-9]+/", "", $table->connection);
        return [$connection, Stringer::instance()->toPascal($connection)];
    }
}