<?php
namespace Presto\Core\Consoles;

use Presto\Core\Utilities\Stringer;
use Presto\Core\Utilities\Pather;

class HelperCommand extends \Presto\Core\Consoles\Command
{
    protected $description = "helpers.php ヘルパーの作成";

    public function handler()
    {
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
class Controller extends \Presto\Core\Controller {}

{$phpcomment}
class Service extends \Presto\Core\Service {}

{$phpcomment}
class Command extends \Presto\Core\Consoles\Command {}
EOF;

        // facade.phpを生成する
        file_put_contents($filename, $data);
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
        $classname = "\\App".Stringer::instance()->toPascal($classname);

        return $classname;
    }

    // 変数名
    private function getPropertyNameByClass(string $class)
    {
        $name = Stringer::instance()->toCamel(preg_replace("/^.+\\\\/", "", $class));
        return str_replace("Repository", "", $name);
    }
}