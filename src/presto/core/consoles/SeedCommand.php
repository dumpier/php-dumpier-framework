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
    protected $base_path = "app/resources/database/seeds";
    protected $services = [];

    public function handler(...$parameters)
    {

        $this->directories();
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
        $time_start = microtime(true);
        $this->info(" - {$csvfile}");

        $connection = QueryBuilder::instance()->connect($dbname);

        $table = preg_replace("/.*\/(.+?)\.csv/", "$1", $csvfile);

        // 元のデータをバックアップ
        // $rows = QueryBuilder::instance()->connect($dbname)->select($table);

        // 既存データをTRUNCATE
        $connection->truncate($table);

        // CSVをロードする
        $rows = CsvLoader::instance()->getBody($csvfile);
        CsvLoader::instance()->clear();
        $count = count($rows);

        // CSVデータをDBに登録する
        $connection->bulkInsert($table, $rows);

        // 実行時間
        $time_end = microtime(true);
        $time_exe = round($time_end - $time_start, 3);

        // メモリ
        $memory = UnitUtility::instance()->mega(memory_get_usage(), 2);

        $this->info("   Import to [{$dbname}].[{$table}]\t{$count} rows completed !");
        $this->info("   Time : {$time_exe} Sec\tMemory : {$memory} Mb");
        $this->info("");
    }

}