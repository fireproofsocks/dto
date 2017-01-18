<?php
namespace Dto\Validators;

use Dto\Exceptions\InvalidScalarValueException;

class StringValidator extends AbstractValidator implements ValidatorInterface
{
    public function validate($string) {
    // A string instance is valid against this keyword if its length is less than, or equal to, the value of this keyword.
        if ($maxLength = $this->schema->getMaxLength()) {
            if (strlen($string) > $maxLength) {
                throw new InvalidScalarValueException('Length of string failed "maxLength".');
            }
        }
        // A string instance is valid against this keyword if its length is greater than, or equal to, the value of this keyword.
        if ($minLength = $this->schema->getMinLength()) {
            if (strlen($string) > $maxLength) {
                throw new InvalidScalarValueException('Length of string failed "minLength".');
            }
        }
        if ($pattern = $this->schema->getPattern()) {
            if (!preg_match('/'.$pattern.'/', $string)) {
                throw new InvalidScalarValueException('Valud of string failed regular expression in "pattern".');
            }
        }

        return true;
    }
}