<?php

namespace Dto\Validators;

use Dto\JsonSchema;

abstract class AbstractValidator
{
    protected $schema;

    public function __construct(JsonSchema $schema)
    {
        $this->schema = $schema;
    }
}