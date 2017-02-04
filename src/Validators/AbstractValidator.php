<?php

namespace Dto\Validators;

abstract class AbstractValidator
{
    protected $container;

    protected $schemaAccessor;

    public function __construct(\ArrayAccess $container)
    {
        $this->container = $container;
    }
}