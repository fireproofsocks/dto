<?php

namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidNumberValueException;
use Dto\Exceptions\InvalidScalarValueException;
use Dto\JsonSchemaAccessorInterface;
use Dto\TypeDetectorInterface;
use Dto\Validators\AbstractValidator;
use Dto\Validators\ValidatorInterface;

class NumberValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * Check multipleOf, maximum, exclusiveMaximum, minimum, exclusiveMinimum
     * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.1
     * @param $number number
     * @param $schema array
     * @return bool
     * @throws InvalidScalarValueException
     */
    public function validate($number, array $schema)
    {
        $this->schemaAccessor = $this->container[JsonSchemaAccessorInterface::class]->load($schema);

        $this->checkDataType($number);
        $this->checkMultipleOf($number);
        $this->checkMaximum($number);
        $this->checkMinimum($number);

        return true;
    }

    protected function checkDataType($number)
    {
        if (!$this->container[TypeDetectorInterface::class]->isNumber($number)) {
            throw new InvalidNumberValueException('Value is not numeric.');
        }
    }

    protected function checkMultipleOf($number)
    {
        // A numeric instance is only valid if division by this keyword's value results in an integer.
        if ($multipleOf = $this->schemaAccessor->getMultipleOf()) {
            // we have to use fmod (float) because the modulo operator (%) coverts operands to integers
            if (fmod($number, $multipleOf) != 0) {
                throw new InvalidScalarValueException('Division by "multipleOf" value does not result in an integer.');
            }
        }
    }

    protected function checkMaximum($number)
    {
        $maximum = $this->schemaAccessor->getMaximum();
        if ($maximum !== false) {
            //      number
            // <----valid----O exclusiveMax
            // <----valid----X inclusiveMax
            if ($exclusiveMaximum = $this->schemaAccessor->getExclusiveMaximum()) {
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
        $minimum = $this->schemaAccessor->getMinimum();
        if ($minimum !== false) {
            //      number
            // O-----valid----> exclusiveMin
            // X-----valid----> inclusiveMin
            if ($exclusiveMinimum = $this->schemaAccessor->getExclusiveMinimum()) {
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