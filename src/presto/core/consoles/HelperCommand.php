<?php
namespace Presto\Core\Consoles;

use Presto\Core\Utilities\Stringer;
use Presto\Core\Utilities\Pather;

class HelperCommand extends \Presto\Core\Consoles\Command
{
    protected $description = "helpers.php ヘルパーの作成";

    public function handler()
    {
        $this->info("###################################################");
        $this->info("# START GENERAGE Service, Repository, Model");
        $this->info("###################################################");

        // サービス一覧
        $service_comment = $this->getServiceAndRepositoryList(Pather::instance()->service());
        // リポジトリ一覧
        $repository_comment = $this->getServiceAndRepositoryList(Pather::instance()->repository());

        // サービスとリポジトリ一覧
        $phpcomment = "/**" . PHP_EOL . $service_comment ." *".PHP_EOL. $repository_comment . " */";

        $filename = Pather::instance()->path("helpers.php");

        $data = <<<EOF
<?php
{$phpcomment}
interface InjectableInterface {}

class Controller extends \Presto\Core\Controller implements InjectableInterface {}
class Service extends \Presto\Core\Service implements InjectableInterface {}
class Command extends \Presto\Core\Consoles\Command implements InjectableInterface {}
EOF;

        // facade.phpを生成する
        file_put_contents($filename, $data);
        $this->info("-----------------------------------------------------");
        $this->info(" COMPLETED! ");
        $this->info("-----------------------------------------------------");

    }


    /**
     * Facade用サービスとリポジトリ一覧のPHPDocを生成
     * @param string $root_path
     * @return string
     */
    private function getServiceAndRepositoryList(string $root_path)
    {
        $phpcomment = "";

        foreach (glob("{$root_path}/*") as $path)
        {
            // フォルダの場合、下位の精査する
            if(is_dir($path))
            {
                $this->info("# change folder : {$path}");
                $phpcomment .= $this->getServiceAndRepositoryList($path);
            }

            // PHPファイルでない場合、解析対象外
            if(pathinfo($path, PATHINFO_EXTENSION) != 'php')
            {
                continue;
            }

            // クラス
            $class = $this->getClassByPath($path);

            // 変数名
            $property = $this->getPropertyNameByClass($class);
            $this->info("\t- class: {$class}, property: {$property}");

            // PHPDocコメント
            $phpcomment .= " * @property {$class} \${$property}" . PHP_EOL;
        }

        return $phpcomment;
    }


    // クラス名
    private function getClassByPath(string $path)
    {
        $classname = str_replace(Pather::instance()->class(), "", str_replace(".php", "", $path));
        $classname = str_replace("/", "\\", $classname);
        $classname = "\\App\\".Stringer::instance()->toPascal($classname);

        return $classname;
    }

    // 変数名
    private function getPropertyNameByClass(string $class)
    {
        $name = Stringer::instance()->toPascal(preg_replace("/^.+\\\\/", "", $class));
        return str_replace("Repository", "", $name);
    }
}