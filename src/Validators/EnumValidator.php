<?php
namespace Dto\Validators;

use Dto\Exceptions\InvalidEnumException;
use Dto\JsonSchemaAccessorInterface;

class EnumValidator extends AbstractValidator implements ValidatorInterface
{
    public function validate($value, array $schema)
    {
        $this->schemaAccessor = $this->container[JsonSchemaAccessorInterface::class]->load($schema);

        $enum = $this->schemaAccessor->getEnum();

        if ($enum !== false) {
            if (!in_array($value, $enum, true)) {
                throw new InvalidEnumException('Value not allowed in "enum".');
            }
        }

        return true;
    }

}