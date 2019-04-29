<?php
namespace Presto\Core\Utilities\Files;

use Presto\Core\Traits\Singletonable;

class FileLoader
{
    use Singletonable;

    public function isJson(string $path)
    {
        return $this->extensionIs($path, "json");
    }

    public function isCsv(string $path)
    {
        return $this->extensionIs($path, "csv");
    }

    public function extensionIs(string $path, string $extension)
    {
        if($this->extension($path) == $extension)
        {
            return true;
        }

        return false;
    }

    public function extension(string $path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

}