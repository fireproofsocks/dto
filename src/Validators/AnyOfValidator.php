<?php

namespace Dto\Validators;

use Dto\JsonSchemaAccessorInterface;

class AnyOfValidator extends AbstractValidator implements ValidatorInterface
{
    public function validate($value, array $schema)
    {
        $this->schemaAccessor = $this->container[JsonSchemaAccessorInterface::class]->load($schema);

        $anyOf = $this->schema->getAnyOf();
    }

}