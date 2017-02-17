<?php
namespace DtoTest\Regulator;

use Dto\ServiceContainerInterface;
use Pimple\Container;

class MockContainer implements ServiceContainerInterface
{
    protected $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function make($service)
    {
        return $this->container[$service];
    }

    public function bind($service, \Closure $closure)
    {
        $this->container[$service] = $closure;
    }

}