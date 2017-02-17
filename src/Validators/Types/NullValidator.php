<?php

namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidDataTypeException;
use Dto\TypeDetectorInterface;
use Dto\Validators\AbstractValidator;
use Dto\Validators\ValidatorInterface;

class NullValidator extends AbstractValidator implements ValidatorInterface
{
    public function validate($value, array $schema)
    {
        if (!$this->container->make(TypeDetectorInterface::class)->isNull($value)) {
            throw new InvalidDataTypeException('"type":"null" allows only null values. Pass a literal null or define your "type" as an array of types.');
        }

        return $value;
    }
}