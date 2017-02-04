<?php

namespace Dto;

use Dto\Validators\AnyOfValidator;
use Dto\Validators\EnumValidator;
use Dto\Validators\TypeValidator;

class ValidatorSelector implements ValidatorSelectorInterface
{
    protected $container;

    protected $schemaAccessor;

    public function __construct(\ArrayAccess $container)
    {
        $this->container = $container;
        $this->schemaAccessor = $container[JsonSchemaAccessorInterface::class];
    }

    public function selectValidators(array $schema)
    {

        $this->schemaAccessor = $this->schemaAccessor->load($schema);

        $validators = [];

        $enum = $this->schemaAccessor->getEnum();
        if ($enum !== false) {
            $validators[] = $this->container[EnumValidator::class];
        }

        // TODO: oneOf, not

        $anyOf = $this->schemaAccessor->getAnyOf();
        if ($anyOf !== false) {
            $validators[] = $this->container[AnyOfValidator::class];
        }

        $type = $this->schemaAccessor->getType();


        if ($type !== false) {
            $validators[] = $this->container[TypeValidator::class];
        }

        return $validators;

    }

}