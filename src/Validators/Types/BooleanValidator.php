<?php

namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidBooleanValueException;
use Dto\TypeDetectorInterface;
use Dto\Validators\AbstractValidator;
use Dto\Validators\ValidatorInterface;

class BooleanValidator extends AbstractValidator implements ValidatorInterface
{
    public function validate($value, array $schema)
    {
        if (!$this->container[TypeDetectorInterface::class]->isBoolean($value)) {
            throw new InvalidBooleanValueException('Value is not a boolean.');
        }

        return true;
    }
}