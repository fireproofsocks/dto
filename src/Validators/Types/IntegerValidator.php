<?php

namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidIntegerValueException;
use Dto\TypeDetectorInterface;
use Dto\Validators\ValidatorInterface;

class IntegerValidator extends NumberValidator implements ValidatorInterface
{
    protected function checkDataType($number)
    {
        if (!$this->container[TypeDetectorInterface::class]->isInteger($number)) {
            throw new InvalidIntegerValueException('Value is not an integer.');
        }
    }
}