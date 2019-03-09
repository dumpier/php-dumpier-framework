<?php
namespace Presto\Databases\Model\Manages;

/**
 * TODO xxx_1~nのような項目でJOINする場合、シンプルに記述できるようにしたい
 *      例）$slices = ["player_character_id"=>n,]
 */
class SliceManage
{
    protected $slices;

    public function __construct(array $slices)
    {
        foreach ($slices as $field=>$count)
        {
            for( $i=1; $i<= $count; $i++ )
            {
                $this->slices[$field][] = "{$field}_{$i}";
            }
        }
    }


    public function all()
    {
        return $this->slices;
    }


    public function get(string $field)
    {
        if( empty($this->slices[$field]) )
        {
            return [$field];
        }

        return $this->slices[$field];
    }

}