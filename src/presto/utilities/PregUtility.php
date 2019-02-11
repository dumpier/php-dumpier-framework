<?php
namespace Presto\Utilities;

use Presto\Traits\Singletonable;

class PregUtility
{
    use Singletonable;

    public static function all(string $pattern, string $text)
    {
        preg_match_all($pattern, $text, $rows);

        return $rows[0];
    }
}