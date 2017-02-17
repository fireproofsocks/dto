<?php
namespace DtoTest\Regulator;

use Dto\ServiceContainer;
use Pimple\Container;

class MockContainer extends ServiceContainer
{
    protected $container;

    public function __construct()
    {
        // Override the init() stuff in parent
        $this->container = new Container();
    }
}