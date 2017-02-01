<?php
namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidScalarValueException;
use Dto\JsonSchemaAccessorInterface;
use Dto\TypeDetectorInterface;
use Dto\Validators\AbstractValidator;
use Dto\Validators\ValidatorInterface;

class StringValidator extends AbstractValidator implements ValidatorInterface
{
    public function validate($string, array $schema)
    {
        $this->schemaAccessor = $this->container[JsonSchemaAccessorInterface::class]->load($schema);
        if ($this->container[TypeDetectorInterface::class]->isString($value)) {

        }
        // A string instance is valid against this keyword if its length is less than, or equal to, the value of this keyword.
        if ($maxLength = $this->schemaAccessor->getMaxLength()) {
            if (strlen($string) > $maxLength) {
                throw new InvalidScalarValueException('Length of string failed "maxLength".');
            }
        }
        // A string instance is valid against this keyword if its length is greater than, or equal to, the value of this keyword.
        if ($minLength = $this->schemaAccessor->getMinLength()) {
            if (strlen($string) > $maxLength) {
                throw new InvalidScalarValueException('Length of string failed "minLength".');
            }
        }
        if ($pattern = $this->schemaAccessor->getPattern()) {
            if (!preg_match('/'.$pattern.'/', $string)) {
                throw new InvalidScalarValueException('Valud of string failed regular expression in "pattern".');
            }
        }

        return true;
    }
}