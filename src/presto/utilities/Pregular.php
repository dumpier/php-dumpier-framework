<?php
namespace Presto\Utilities;

use Presto\Traits\Singletonable;

class Pregular
{
    use Singletonable;

    public function all(string $pattern, string $text)
    {
        preg_match_all($pattern, $text, $rows);

        return $rows[0];
    }

    public function first(string $pattern, string $text)
    {
        preg_match($pattern, $text, $row);

        return $row;
    }

}