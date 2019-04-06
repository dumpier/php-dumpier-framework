<?php
namespace Presto\Core\Utilities\Files;

use Presto\Core\Traits\Singletonable;
use Presto\Core\Utilities\Arrayer;
use Presto\Core\Utilities\Pather;

class ConfigLoader
{
    use Singletonable;

    private $configs = [];

    const CONFIG_LIST = [
        'app', 'cache', 'database', 'session', 'logging', 'debugbar',
    ];

    public function get(string $filename, string $key="")
    {
        $configs = $this->getByCache($filename);

        if(empty($key))
        {
            return $configs;
        }

        if(empty($key))
        {
            return null;
        }

        return Arrayer::instance()->get($configs, $key);
    }


    private function getByCache(string $filename)
    {
        // キャッシュがある場合
        if(empty($this->configs[$filename]))
        {
            $config_path = Pather::instance()->path("config/{$filename}.php");

            if( ! file_exists( $config_path ) )
            {
                throw new \Exception("configファイルが見つからない[{$filename}]");
            }

            $this->configs[$filename] = include $config_path;
        }

        return $this->configs[$filename];
    }

}