<?php
namespace Presto;

use Presto\Traits\Injectable;

class Service
{
    use Injectable;

    protected $services;
    protected $repositories;
}