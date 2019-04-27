<?php
namespace Presto\Core\Utilities;

use Presto\Core\Traits\Singletonable;

class Pather
{
    use Singletonable;

    protected $root = "";
    protected $app = "";
    protected $framework = "";


    public function __construct()
    {
        // TODO
        if( is_link("/vagrant/code/github/dumpieer/php-presto-app/vendor/dumpier/php-presto-framework") )
        {
            $this->root = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . "/php-presto-app";
        }
        else
        {
            //  TODO 簡略化というか、このファイルの場所が変わる都度変わるのを何とかする
            $this->root = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))));
        }
    }


    /** @return string */
    public function path(string $path="")
    {
        return Stringer::instance()->cleanDirectorySeparator("{$this->root}/{$path}");
    }

    /** @return string */
    public function framework(string $path="")
    {
        //  TODO 簡略化というか、このファイルの場所が変わる都度変わるのを何とかする
        $root = dirname(dirname(dirname(dirname(__DIR__))));
        return Stringer::instance()->cleanDirectorySeparator("{$root}/{$path}");
    }

    /** @return string */
    public function app(string $path="") { return path("app/{$path}"); }
    /** @return string */
    public function config(string $path="") { return path("config/{$path}"); }
    /** @return string */
    public function resource(string $path="") { return path("app/resources/{$path}"); }

    /** @return string */
    public function class(string $path="") { return path("app/classes/{$path}"); }
    /** @return string */
    public function controller(string $path="") { return path("app/classes/http/controllers/{$path}"); }
    /** @return string */
    public function service(string $path="") { return path("app/classes/services/{$path}"); }
    /** @return string */
    public function repository(string $path="") { return path("app/classes/models/repositories/{$path}"); }

    /** @return string */
    public function template(string $path="") { return path("app/templates/{$path}"); }


    /** @return string */
    public function storage(string $path="") { return path("storages/{$path}"); }
    /** @return string */
    public function debugbar(string $path="") { return path("storages/debugbar/{$path}"); }

    /** @return string */
    public function cache(string $path="") { return path("storages/cache/{$path}"); }
    /** @return string */
    public function cache_template(string $path="") { return path("storages/cache/templates/{$path}"); }

    /** @return string */
    public function switching(string $root="res", string $path="") {
        switch($root)
        {
            case "log":
                return $this->debugbar($path);

            case "res":
            default:
                return $this->resource($path);
        }
    }
}