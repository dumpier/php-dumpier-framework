<?php
namespace Presto\Utilities;

class PregUtility
{
    public static function getAll(string $pattern, string $text)
    {
        preg_match_all($pattern, $text, $rows);

        return $rows[0];
    }
}