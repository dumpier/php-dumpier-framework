<?php
namespace Presto\Core\Traits;

/**
 * TODO メモリキャッシュ
 */
class Memorable
{
    /** @var array キャッシュ制限個数 */
    private $limit = [];

    /** @var array キャッシュ */
    private $caches = [];


    public function get(string $name, string $key)
    {
        $class = get_class($this);

        if(isset($this->caches[$class][$name]))
        {
            return $this->caches[$class][$name];
        }

        return null;
    }


    public function set(string $name, string $key, $value)
    {
        $class = get_class($this);

        $this->caches[$class][$name] = $value;

        return $this;
    }


    /**
     * 制限個数の指定
     * @param string $key
     * @param int $limit
     * @return \Presto\Core\Traits\Memorable
     */
    public function limit(string $name, int $limit)
    {
        $class = get_class($this);

        $this->limit[$class][$name] = $limit;
        return $this;
    }
}