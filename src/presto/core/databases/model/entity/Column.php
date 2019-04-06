<?php
namespace Presto\Core\Databases\Model\Entity;


class Column
{
    const DATA_TYPE_LIST = [
        'bool'=>['boolean',],
        'int'=>['int','tinyint', 'smallint', 'mediumint', 'bigint',],
        'float'=>['float', 'double', 'decimal', ],
        'string'=>['varchar', 'char', 'text', 'timestamp', 'date', 'datetime', 'json'],
    ];

    public $Field;
    public $Type;
    public $Collation;
    public $Null;
    public $Key;
    public $Default;
    public $Extra;
    public $Privileges;
    public $Comment;


    public function __construct(array $columns)
    {
        foreach ((array)$this as $property=>$val)
        {
            if(isset($columns[$property]))
            {
                $this->{$property} = $columns[$property];
            }
        }
    }


    /**
     * PHPのデータ型
     * @return string
     */
    public function getType()
    {
        foreach (self::DATA_TYPE_LIST as $php_type=>$mysqlTypeList)
        {
            foreach ($mysqlTypeList as $type)
            {
                if( preg_match("/".$type."/", $this->Type) )
                {
                    return $php_type;
                }
            }
        }

        return "mixed";
    }


    /**
     * デフォルト値のPHPコード
     * @return string|mixed
     */
    public function getDefaultValueExpression()
    {
        $val = $this->getDefaultValue();

        if("string"==$this->getType())
        {
            if( in_array($this->Type, ["date", "datetime", "timestamp"]) )
            {
                return "NULL";
            }

            return '"' . $val . '"';
        }

        if(empty($val))
        {
            return 0;
        }

        return $val;
    }


    /**
     * デフォルト値の取得
     */
    private function getDefaultValue()
    {
        return $this->Default;
    }
}