<?php

namespace Dto;

use Dto\Validators\AnyOfValidator;
use Dto\Validators\EnumValidator;
use Dto\Validators\TypeValidator;

/**
 * Class ValidatorSelector
 * Finds/collects any top-level validators.
 * @package Dto
 */
class ValidatorSelector implements ValidatorSelectorInterface
{
    protected $container;

    protected $schemaAccessor;

    public function __construct(ServiceContainerInterface $container)
    {
        $this->container = $container;
        $this->schemaAccessor = $container->make(JsonSchemaAccessorInterface::class);
    }

    public function selectValidators(array $schema)
    {

        $this->schemaAccessor = $this->schemaAccessor->load($schema);

        $validators = [];

        $enum = $this->schemaAccessor->getEnum();
        if ($enum !== false) {
            $validators[] = $this->container->make(EnumValidator::class);
        }

        // TODO: oneOf, not

        $anyOf = $this->schemaAccessor->getAnyOf();
        if ($anyOf !== false) {
            $validators[] = $this->container->make(AnyOfValidator::class);
        }

        $type = $this->schemaAccessor->getType();


        if ($type !== false) {
            $validators[] = $this->container->make(TypeValidator::class);
        }

        return $validators;

    }

}