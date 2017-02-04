<?php

namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidNullValueException;
use Dto\TypeDetectorInterface;
use Dto\Validators\AbstractValidator;
use Dto\Validators\ValidatorInterface;

class NullValidator extends AbstractValidator implements ValidatorInterface
{
    public function validate($value, array $schema)
    {
        if (!$this->container[TypeDetectorInterface::class]->isNull($value)) {
            throw new InvalidNullValueException('"type":"null" allows only null values. Pass a literal null or define your "type" as an array of types.');
        }

        return $value;
    }
}