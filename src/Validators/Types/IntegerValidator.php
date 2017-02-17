<?php

namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidDataTypeException;
use Dto\TypeDetectorInterface;
use Dto\Validators\ValidatorInterface;

class IntegerValidator extends NumberValidator implements ValidatorInterface
{
    protected function checkDataType($number)
    {
        if (!$this->container->make(TypeDetectorInterface::class)->isInteger($number)) {
            throw new InvalidDataTypeException('Value is not an integer.');
        }
    }
}