<?php

namespace Dto\Validators;

abstract class AbstractValidator
{
    protected $container;

    protected $schemaAccessor;

    protected $isFiltered;

    protected $value;

    public function __construct(\ArrayAccess $container)
    {
        $this->container = $container;
    }

    public function isFilteredValue()
    {
        return $this->isFiltered;
    }

    public function getFilteredValue()
    {
        return $this->value;
    }
}