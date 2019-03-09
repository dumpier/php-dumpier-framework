<?php
namespace Presto\Databases\Model\Entity;

class Table
{
    public $connection;

    public $name;

    /** @var Column[] */
    public $columns = [];

    /**
     *
     * @param string $connection
     * @param string $name
     * @param array $columns
     */
    public function __construct(string $connection, string $name, array $columns)
    {
        $this->connection = $connection;
        $this->name = $name;

        foreach ($columns as $val)
        {
            $column = new Column((array)$val);

            $this->columns[$column->Field] = $column;
        }
    }

}