<?php
namespace Presto\Traits;

/**
 * @property $services
 * @property $repositories
 */
trait Injectable
{
    public function __get(string $property)
    {
        $name = str()->toPascal($property);

        if(preg_match("/Service$/", $name))
        {
            return $this->getInjectClassByName($name, 'service');
        }

        return $this->getInjectClassByName($name,'repository');
    }


    private function getInjectClassByName(string $name, $type='service')
    {
        $classes = $type == 'service' ? $this->services : $this->repositories;

        foreach ($classes as $class)
        {
            if(preg_match("/{$name}/", $class))
            {
                return app($class);
            }
        }

        throw new \Exception("クラスが見当たらない[{$name}]");
    }
}