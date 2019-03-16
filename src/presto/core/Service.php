<?php
namespace Presto;

use Presto\Core\Traits\Injectable;

class Service
{
    use Injectable;

    protected $services;
    protected $repositories;
}