<?php

namespace Dto\Validators;

use Dto\ServiceContainerInterface;

abstract class AbstractValidator
{
    protected $container;

    protected $schemaAccessor;

    public function __construct(ServiceContainerInterface $container)
    {
        $this->container = $container;
    }
}