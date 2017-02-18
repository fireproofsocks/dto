<?php

namespace Dto;

use Dto\Validators\AllOfValidator;
use Dto\Validators\AnyOfValidator;
use Dto\Validators\EnumValidator;
use Dto\Validators\NotValidator;
use Dto\Validators\OneOfValidator;
use Dto\Validators\TypeValidator;

/**
 * Class ValidatorSelector
 * Finds/collects any "top-level" validators.
 * @package Dto
 */
class ValidatorSelector implements ValidatorSelectorInterface
{
    protected $container;

    protected $schemaAccessor;

    public function __construct(ServiceContainerInterface $container)
    {
        $this->container = $container;
    }

    public function selectValidators(array $schema)
    {
        $this->schemaAccessor = $this->container->make(JsonSchemaAccessorInterface::class)->factory($schema);

        $validators = [];

        $enum = $this->schemaAccessor->getEnum();
        if ($enum !== false) {
            $validators[] = $this->container->make(EnumValidator::class);
        }

        $oneOf = $this->schemaAccessor->getOneOf();
        if ($oneOf !== false) {
            $validators[] = $this->container->make(OneOfValidator::class);
        }

        $not = $this->schemaAccessor->getNot();
        if ($not !== false) {
            $validators[] = $this->container->make(NotValidator::class);
        }

        $allOf = $this->schemaAccessor->getAllOf();
        if ($allOf !== false) {
            $validators[] = $this->container->make(AllOfValidator::class);
        }

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