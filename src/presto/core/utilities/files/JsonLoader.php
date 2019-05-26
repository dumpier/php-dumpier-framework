<?php
namespace Presto\Core\Utilities\Files;

use Presto\Core\Traits\Singletonable;

class JsonLoader
{
    use Singletonable;

    /**
     *  ページング
     * @param string $filename
     * @param int $page
     * @param array $parameter
     */
    public function paging(string $filename, int $page=1, array $parameter=[])
    {
        list($lines, $count) = FileLoader::instance()->paging($filename, $page);

        $rows = [];

        foreach ($lines as $line)
        {
            $rows[] = json_decode($line, true);
        }

        return [$rows, $count, []];
    }

}