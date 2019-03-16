<?php
namespace Presto\Core\Consoles;

use Presto\Core\Traits\Injectable;

class Command
{
    use Injectable;

    const INFO = "info";
    const DEBUG = "debug";
    const WARN = "warning";
    const ERROR = "error";

    protected $services = [];
    protected $repositories = [];

    public function handler() { }


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