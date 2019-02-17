<?php
namespace Presto\Consoles;

use Presto\Model\Entity\Table;
use Presto\Services\GenerateModelService;


/**
 * @property \Presto\Services\GenerateModelService $generateModelService
 *
 *
 */
class GenerateModelCommand extends \Presto\Consoles\Command
{
    const IGNORE_TABLE = ['migrations'];

    protected $services = [
        GenerateModelService::class,
    ];

    protected $signature = 'generate:model';
    protected $description = 'Generate Models';
    protected $base_path = "";



    public function handle()
    {
        $this->initializeArguments();

        $this->generate();
    }


    private function initializeArguments()
    {
        $this->base_path = path('storages/generates/app/models/');
    }


    private function generate()
    {
        foreach (config('database.connections') as $connection=>$config)
        {
            $this->info("# Start generate database {$connection}'s models.");

            // テーブル一覧の取得
            $tables = database($connection)->tables();

            foreach ($tables as $val)
            {
                // テーブル名
                $table_name = array_values((array)$val)[0];

                if(in_array($table_name, self::IGNORE_TABLE))
                {
                    $this->warn("  - Skip {$table_name}");
                }

                // 指定テーブルのPHPDoc、Dao、Repositoryの生成
                $this->generateModels($connection, $table_name);
            }

            $this->info("- Completed !");
        }
    }


    /**
     * 指定テーブルのPHPDoc、Dao、Repositoryを生成する
     * @param string $connection
     * @param string $table_name
     */
    private function generateModels(string $connection, string $table_name)
    {
        $this->info("  - Start generate {$table_name}'s models.");

        // 項目定義一覧
        $columns = database($connection)->columns($table_name);

        // テーブル定義を構造化する
        $table = new Table($connection, $table_name, $columns);

        // -----------------------------
        // PHPコードの生成
        // -----------------------------
        // PHPDoc
        list($file, $codes) = $this->generateModelService->generatePHPDoc($table);
        $this->info("    - Start create phpdoc {$file}");
        $this->createPhpFile($file, $codes);

        // Property
        list($file, $codes) = $this->generateModelService->generatePropertyCode($table);
        $this->info("    - Start create property {$file}");
        $this->createPhpFile($file, $codes);

        // Dao
        list($file, $codes) = $this->generateModelService->generateDaoCode($table);
        $this->info("    - Start create dao class {$file}");
        $this->createPhpFile($file, $codes);

        // Repository
        list($file, $codes) = $this->generateModelService->generateRepositoryCode($table);
        $this->info("    - Start create repository class {$file}");
        $this->createPhpFile($file, $codes);

        $this->info("  - Completed {$table_name}!");
    }


    /**
     * ファイルの生成
     * @param string $file
     * @param array $codes
     */
    private function createPhpFile(string $file, array $codes)
    {
        if(empty($file) || empty($codes))
        {
            $this->warn("    - Skip {$file}");
            return false;
        }

        $file = $this->base_path.$file;

        // フォルダーが存在しない場合、作成する
        if(!file_exists($dir = dirname($file)))
        {
            $this->info("  - Create directory {$dir}");
            mkdir($dir, 0755, true);
        }

        $php_context = "<?php" . PHP_EOL;
        foreach ($codes as $code)
        {
            $php_context .= $code . PHP_EOL;
        }

        file_put_contents($file, $php_context);
    }
}