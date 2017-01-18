<?php

namespace Dto\Validators;

use Dto\Exceptions\InvalidScalarValueException;

class NumberValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * Check multipleOf, maximum, exclusiveMaximum, minimum, exclusiveMinimum
     * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.1
     * @param $number number
     * @return bool
     * @throws InvalidScalarValueException
     */
    public function validate($number)
    {
        $this->checkMultipleOf($number);
        $this->checkMaximum($number);
        $this->checkMinimum($number);

        return true;
    }


    protected function checkMultipleOf($number)
    {
        // A numeric instance is only valid if division by this keyword's value results in an integer.
        if ($multipleOf = $this->schema->getMultipleOf()) {
            // we have to use fmod (float) because the modulo operator (%) coverts operands to integers
            if (fmod($number, $multipleOf) != 0) {
                throw new InvalidScalarValueException('Division by "multipleOf" value does not result in an integer.');
            }
        }
    }

    protected function checkMaximum($number)
    {
        $maximum = $this->schema->getMaximum();
        if ($maximum !== false) {
            //      number
            // <----valid----O exclusiveMax
            // <----valid----X inclusiveMax
            if ($exclusiveMaximum = $this->schema->getExclusiveMaximum()) {
                if ($number >= $maximum) {
                    throw new InvalidScalarValueException('Number is greater than or equal to defined "maximum" ('.$maximum.') ("exclusiveMaximum" applied).');
                }
            }
            elseif ($number > $maximum) {
                throw new InvalidScalarValueException('Number is greater than defined "maximum" ('.$maximum.').');
            }

        }
    }

    protected function checkMinimum($number)
    {
        $minimum = $this->schema->getMinimum();
        if ($minimum !== false) {
            //      number
            // O-----valid----> exclusiveMin
            // X-----valid----> inclusiveMin
            if ($exclusiveMinimum = $this->schema->getExclusiveMinimum()) {
                if ($number <= $minimum) {
                    throw new InvalidScalarValueException('Number is less than or equal to defined "minimum" ('.$minimum.') ("exclusiveMinimum" applied).');
                }
            }
            elseif ($number < $minimum) {
                throw new InvalidScalarValueException('Number is less than defined "minimum" ('.$minimum.').');
            }
        }
    }
}