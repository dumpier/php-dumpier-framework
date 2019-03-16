<?php
namespace Presto\Core\Traits;

/**
 * @property array $services
 * @property array $repositories
 */
trait Injectable
{
    // TODO ServicesとRepositories以外も追加可能にする
    protected $injections = [
        "services",
        "repositories",
    ];

    public function __get(string $property)
    {
        $name = stringer()->toPascal($property);

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