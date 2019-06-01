<?php
namespace Presto\Core\Consoles;

use Presto\Core\Utilities\Pather;
use Presto\Core\Utilities\Files\DirectoryLoader;
use Presto\Core\Databases\QueryBuilder;
use Presto\Core\Utilities\Files\CsvLoader;

class SeedCommand extends \Presto\Core\Consoles\Command
{
    protected $signature = 'seed';
    protected $description = 'CSV SEEDER';
    protected $base_path = "app/resources/database/seeds";
    protected $services = [];

    public function handler(string $fullpath="")
    {
        $this->directories($fullpath);
    }


    public function directories(string $fullpath="")
    {
        $fullpath = $fullpath ? $fullpath : Pather::instance()->path($this->base_path);

        $directories = DirectoryLoader::instance()->directories($fullpath);

        foreach ($directories as $directory)
        {
            // フォルダ名よりDB名を取得
            $dbname = preg_replace("/.*\/(.+?)/", "$1", $directory);
            $this->info(" # {$dbname}");

            $this->csvs($directory, $dbname);
        }
    }


    public function csvs(string $fullpath="", string $dbname)
    {
        $fullpath = $fullpath ? $fullpath : Pather::instance()->path($this->base_path);
        $csvfiles = DirectoryLoader::instance()->files($fullpath);

        foreach ($csvfiles as $csvfile)
        {
            $this->csv($csvfile, $dbname);
        }
    }


    public function csv(string $csvfile, string $dbname)
    {
        $connection = QueryBuilder::instance()->connect($dbname);

        $table = preg_replace("/.*\/(.+?)\.csv/", "$1", $csvfile);

        // 元のデータをバックアップ
        // $rows = QueryBuilder::instance()->connect($dbname)->select($table);

        // 既存データをTRUNCATE
        $connection->truncate($table);

        // CSVをロードする
        $rows = CsvLoader::instance()->getBody($csvfile);
        $count = count($rows);

        // CSVデータをDBに登録する
        $connection->bulkInsert($table, $rows);

        $this->info("  - Import [{$dbname}].[{$table}]\t{$count} rows completed !");
    }

}