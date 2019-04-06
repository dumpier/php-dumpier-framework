<?php
namespace Presto\Facade;

use Presto\Core\Traits\Singletonable;

class DatabaseFacade
{
    use Singletonable;

    /** @return mixed */
    function where(array $conditions=[])
    {
        return \Presto\Core\Databases\QueryToWhere::instance()->convert( $conditions );
    }

    /** @return \Presto\Core\Databases\QueryBuilder */
    function database(string $name="", string $database="") {
        if(empty($name) && empty($database))
        {
            return \Presto\Core\Databases\QueryBuilder::instance();
        }
        return \Presto\Core\Databases\QueryBuilder::instance()->connect($name, $database);
    }


    /** @return mixed */
    function select(string $query, array $binds=[], $name=null)
    {
        return \Presto\Core\Databases\QueryBuilder::instance()->select($query, $binds, $name);
    }


}