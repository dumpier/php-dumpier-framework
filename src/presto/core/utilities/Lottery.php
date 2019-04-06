<?php
namespace Presto\Core\Utilities;

use Presto\Core\Traits\Singletonable;

class Lottery
{
    use Singletonable;

    /**
     * 重みによるドロップ
     * @param array|mixed $rows
     * @param string $column_key
     * @throws \Exception
     * @return array|mixed
     */
    public function drop($rows, string $column_key="weight")
    {
        $total_weight = array_sum(array_column($rows, $column_key));
        $random = mt_rand(0, $total_weight - 1);

        $weigth = 0;
        foreach ($rows as $row)
        {
            $weigth += $row[$column_key];

            if($weigth >= $random)
            {
                return $row;
            }
        }

        throw new \Exception("Lottery::dropエラー, total:{$total_weight}, random:{$random}");
    }
}