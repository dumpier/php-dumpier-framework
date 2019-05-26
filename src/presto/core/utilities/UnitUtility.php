<?php
namespace Presto\Core\Utilities;

use Presto\Core\Traits\Singletonable;

class UnitUtility
{
    use Singletonable;

    public function kilo(int $bytes, int $precision=1) { return round($bytes / 1024, $precision); }
    public function mega(int $bytes, int $precision=1) { return round($bytes / (1024 * 1024), $precision); }

    public function auto(int $bytes)
    {
        if($bytes < 1024) {
            return "<span style='color:#17a2b8;'>{$bytes} Byte</span>";
        }

        if($bytes < (1024*1024)) {
            return "<span style='color:#007bff;'>" . round($bytes / (1024), 2) . " Kb</span>";
        }

        if($bytes < (1024*1024*1024)) {
            return "<span style='color:#fd7e14;'>" . round($bytes / (1024*1024), 2) . " Mb</span>";
        }

        // 10Mega以上
        if($bytes < (1024*1024*10)) {
            return "<span style='color:#dc3545;'>" . round($bytes / (1024*1024), 2) . " Mb</span>";
        }
    }

}