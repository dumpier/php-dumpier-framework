<?php
namespace Presto\Consoles;

use Presto\Traits\Injectable;

class Command
{
    use Injectable;

    protected $services = [];
    protected $repositories = [];

    public function handler() { }
}