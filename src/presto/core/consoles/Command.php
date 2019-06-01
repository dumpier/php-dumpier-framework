<?php
namespace Presto\Core\Consoles;

use Presto\Core\Traits\Injectable;

class Command
{
    use Injectable;
    protected $services = [];
    protected $repositories = [];
    protected $signature = "";
    protected $description = "";

    const INFO = "info";
    const DEBUG = "debug";
    const WARN = "warning";
    const ERROR = "error";


    public function bootup()
    {
        $timestart = microtime(true);

        $this->info("###################################################");
        $this->info("# START {$this->description}");
        $this->info("###################################################");

        // ハンドラー
        $this->handler();

        $timeend = microtime(true);

        $this->info("-----------------------------------------------------");
        $this->info("# RESULT ");
        $this->info(" - Time : " . round(($timeend - $timestart), 3) . " Sec");
        $this->info(" - Memory : " . util()->unit()->mega(memory_get_peak_usage(TRUE)) . " MB");
        $this->info("-----------------------------------------------------");
    }

    public function handler(){}


    public function info(string $msg, array $datas=[])
    {
        $this->out($msg, $datas, self::INFO);
    }

    public function debug(string $msg, array $datas=[])
    {
        $this->out($msg, $datas, self::DEBUG);
    }

    public function warn(string $msg, array $datas=[])
    {
        $this->out($msg, $datas, self::WARN);
    }

    public function error(string $msg, array $datas=[])
    {
        $this->out($msg, $datas, self::ERROR);
    }

    // TODO
    public function out(string $msg, array $datas=[], string $type=self::INFO)
    {
        echo "{$msg}" . PHP_EOL;
        if($datas)
        {
            print_r($datas);
            echo PHP_EOL;
        }
    }
}