<?php
namespace Presto\Utilities;

use Presto\Traits\Singletonable;
use Presto\Model\Model;

class Arrayer
{
    use Singletonable;

    public function get(array $array, $path, $separator = '.')
    {
        $keys = explode($separator, trim($path));
        $current = $array;

        foreach ($keys as $key)
        {
            if (!isset($current[$key]))
            {
                return;
            }

            $current = $current[$key];
        }

        return $current;
    }

    public function set(array &$array, $path, $value, $separator = '.')
    {
        $keys = explode($separator, $path);
        $current = &$array;

        foreach ($keys as $key)
        {
            $current = &$current[$key];
        }

        $current = $value;
    }


    public function unset(array &$array, $path, $separator = '.')
    {
        $keys = explode($separator, $path);
        $current = &$array;
        $parent = &$array;

        foreach ($keys as $i => $key)
        {
            if (!array_key_exists($key, $current))
            {
                return;
            }

            if ($i)
            {
                $parent = &$current;
            }

            $current = &$current[$key];
        }

        unset($parent[$key]);
    }


    public function depth(array $array, $depth = 0)
    {
        if (is_array($array) && count($array))
        {
            ++$depth;
            $_c = array($depth);

            foreach ($array as $v)
            {
                if (is_array($v) && count($v))
                {
                    $_c[] = $this->depth($v, $depth);
                }
            }

            return max($_c);
        }

        return $depth;
    }


    public function getKeys(array $array, array $condition=[])
    {
        // 検索条件に該当したCSVの行番号一覧
        $target_keys = [];

        foreach ($condition as $field=>$val)
        {
            // 条件に該当したCSVの行番号一覧
            $keys = array_keys(array_column($array, $field), $val);

            // AND条件で行番号を絞る
            if(empty($target_keys))
            {
                $target_keys = $keys;
            }
            else
            {
                $target_keys = array_intersect($target_keys, $keys);
            }
        }

        return $target_keys;
    }


    /**
     * 二つの配列を結合する TODO TODO TODO TODO
     * @param array $rows
     * @param array $foreigns
     * @param array $joins JOIN Condition
     * @param string $type
     * @return array
     */
    public function mapping(array $rows, array $childrens, array $joins, string $type=Model::HAS_MANY)
    {
        foreach ($rows as $no=>$row)
        {
            foreach ($joins as $foreign_name=>$mappings)
            {
                $keys = array_keys($mappings);
                $values = array_map(function($key)use ($row){ return $row[$key]; }, $keys);
                $foreign_keys = array_values($mappings);

                $condition = array_combine($foreign_keys, $values);

                $rows[$no][$foreign_name] = ($type==Model::HAS_MANY) ? collection($childrens)->get($condition) : collection($childrens)->first($condition);
            }
        }

        return $rows;
    }


    /**
     * 無効な入力をなくす
     * @param array $array
     * @return mixed|array
     */
    public function clean(array $array)
    {
        foreach ($array as $key=>$val)
        {
            if($this->isNotTarget($val))
            {
                unset ($array[$key]);
                continue;
            }

            if(is_array($val))
            {
                $clean = $this->clean($val);
                if($this->isNotTarget($clean))
                {
                    unset ($array[$key]);
                }
                else
                {
                    $array[$key] = $clean;
                }
            }
        }

        return $array;
    }


    private function isNotTarget($val)
    {
        if($val==="" || $val===null || $val===[])
        {
            return true;
        }

        return false;
    }

}