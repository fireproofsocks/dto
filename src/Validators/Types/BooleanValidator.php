<?php

namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidDataTypeException;
use Dto\TypeDetectorInterface;
use Dto\Validators\AbstractValidator;
use Dto\Validators\ValidatorInterface;

class BooleanValidator extends AbstractValidator implements ValidatorInterface
{
    public function validate($value, array $schema)
    {
        if (!$this->container->make(TypeDetectorInterface::class)->isBoolean($value)) {
            throw new InvalidDataTypeException('Value is not a boolean.');
        }

        return $value;
    }
}