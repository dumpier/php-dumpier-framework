<?php
namespace Presto\Core;

use Presto\Core\Traits\Injectable;

class Service
{
    use Injectable;
    protected $repositories = [];
    protected $services = [];
}