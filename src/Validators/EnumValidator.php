<?php
namespace Dto\Validators;

use Dto\Exceptions\InvalidEnumException;

class EnumValidator extends AbstractValidator implements ValidatorInterface
{
    public function validate($value)
    {
        $enum = $this->schema->getEnum();
        if ($enum !== false) {
            if (!in_array($value, $enum, true)) {
                throw new InvalidEnumException('Value not allowed in "enum".');
            }
        }

        return true;
    }

}