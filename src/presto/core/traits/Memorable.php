<?php
namespace Presto\Core\Traits;

/**
 * メモリキャッシュ
 * @property array $statusables
 */
trait Memorable
{
    /** キャッシュ配列 */
    protected static $memory = [];

    /**
     * セット
     * @param string $key
     * @param mixed $data
     */
    public function setMemory(string $key, $data)
    {
        self::$memory[$key] = $data;
    }

    /**
     * ゲット
     * @param string $key
     * @return string|mixed
     */
    public function getMemory(string $key)
    {
        if (! array_key_exists($key, self::$memory) ) {
            return 'nothing-in-memory';
        }

        return self::$memory[$key];
    }

    // ---------------------------------------------------------
    // 自動判定
    // ---------------------------------------------------------
    public function __call(string $name, array $arguments)
    {
        $method = ltrim($name, "_");
        if (method_exists($this, $method)) {
            throw new \Exception("");
        }

        $key = $this->getMemoryKey($name, $arguments);

        if (! array_key_exists($key, self::$memory) ) {
            self::$memory[$key] = call_user_func_array([$this, $method], $arguments);
        }

        return self::$memory[$key];
    }

    /**
     * キャッシュキー
     * @param string $name
     * @param array $arguments
     * @return string
     */
    protected function getMemoryKey(string $name, array $arguments)
    {
        $statusables = $this->statusables ?? [];

        $status = [];

        foreach ($statusables as $statusable) {
            $status[$statusable] = $this->{$statusable};
        }

        return md5(serialize([$name, $arguments, $status]));
    }
    // ---------------------------------------------------------
}