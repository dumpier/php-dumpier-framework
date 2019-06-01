<?php
namespace Presto\Core\Consoles;

use Presto\Core\Utilities\Pather;
use Presto\Core\Utilities\Files\DirectoryLoader;
use Presto\Core\Databases\QueryBuilder;
use Presto\Core\Utilities\Files\CsvLoader;
use Presto\Core\Utilities\UnitUtility;

class SeedCommand extends \Presto\Core\Consoles\Command
{
    protected $signature = 'seed';
    protected $description = 'CSV SEEDER';
    protected $base_path = "app/resources/csvs";

    protected $services = [
    ];

    public function handler()
    {
        $this->info("###################################################");
        $this->info("# START LOAD CSV");
        $this->info("###################################################");

        $this->directories();

        $this->info("-----------------------------------------------------");
        $this->info(" Memory : " . UnitUtility::instance()->mega(memory_get_peak_usage()) . " MB");
        $this->info(" COMPLETED! ");
        $this->info("-----------------------------------------------------");
    }


    private function directories(string $fullpath="")
    {
        $fullpath = $fullpath ? $fullpath : Pather::instance()->path($this->base_path);

        $directories = DirectoryLoader::instance()->directories($fullpath);

        foreach ($directories as $directory)
        {
            $connection_name = preg_replace("/.*\/(.+?)/", "$1", $directory);
            $this->imports($directory, $connection_name);
        }
    }


    private function imports(string $fullpath="", string $connection_name)
    {
        $fullpath = $fullpath ? $fullpath : Pather::instance()->path($this->base_path);
        $csvfiles = DirectoryLoader::instance()->files($fullpath);

        foreach ($csvfiles as $csvfile)
        {
            $this->import($csvfile, $connection_name);
        }

    }

    private function import(string $csvfile, string $connection_name)
    {
        $connection = QueryBuilder::instance()->connect($connection_name);

        $table = preg_replace("/.*\/(.+?)\.csv/", "$1", $csvfile);

        // 元のデータをバックアップ
        // $rows = QueryBuilder::instance()->connect($connection_name)->select($table);

        // 既存データをTRUNCATE
        $connection->truncate($table);

        // CSVをロードする
        $rows = CsvLoader::instance()->getBody($csvfile);
        $count = count($rows);

        // CSVデータをDBに登録する
        $connection->bulkInsert($table, $rows);

        $this->info(" - Import [{$connection_name}].[{$table}]\t{$count} rows completed !");
    }

}