<?php
namespace Presto\Core;

use Presto\Core\Traits\Singletonable;
use Presto\Core\Utilities\Files\ConfigLoader;

class Presto
{
    use Singletonable;

    /** @return string */
    public function baseuri() { return ConfigLoader::instance()->get("app", "baseuri"); }
    /** @return string */
    public function domain() { return $_SERVER['HTTP_HOST']; }
    /** @return string */
    public function protocol() { return $_SERVER["SERVER_PROTOCOL"]; }


    /** @return object */
    public function app(string $class, ...$parameters)
    {
        if(class_exists($class))
        {
            return new $class(...$parameters);
        }

        throw new \Exception("クラス参照エラー[{$class}]");
    }

}
