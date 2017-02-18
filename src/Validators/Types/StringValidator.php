<?php
namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidDataTypeException;
use Dto\Exceptions\InvalidFormatException;
use Dto\Exceptions\InvalidScalarValueException;
use Dto\JsonSchemaAccessorInterface;
use Dto\TypeDetectorInterface;
use Dto\Validators\AbstractValidator;
use Dto\Validators\Types\String\FormatValidatorInterface;
use Dto\Validators\ValidatorInterface;

class StringValidator extends AbstractValidator implements ValidatorInterface
{
    public function validate($string, array $schema)
    {
        $this->schemaAccessor = $this->container->make(JsonSchemaAccessorInterface::class)->factory($schema);

        $this->checkDataType($string);
        $this->checkMaxLength($string);
        $this->checkMinLength($string);
        $this->checkPattern($string);
        $this->checkFormat($string);

        return $string;
    }

    protected function checkDataType($value)
    {
        if (!$this->container->make(TypeDetectorInterface::class)->isString($value)) {
            throw new InvalidDataTypeException('Value is not a string.');
        }
    }

    /**
     * A string instance is valid against this keyword if its length is less than, or equal to, the value of
     * this keyword.
     */
    protected function checkMaxLength($string)
    {

        if ($maxLength = $this->schemaAccessor->getMaxLength()) {
            if (strlen($string) > $maxLength) {
                throw new InvalidScalarValueException('Length of string failed "maxLength".');
            }
        }
    }

    /**
     * A string instance is valid against this keyword if its length is greater than, or equal to, the value of
     * this keyword.
     */
    protected function checkMinLength($string)
    {
        if ($minLength = $this->schemaAccessor->getMinLength()) {
            if (strlen($string) < $minLength) {
                throw new InvalidScalarValueException('Length of string failed "minLength".');
            }
        }
    }

    /**
     * @link https://spacetelescope.github.io/understanding-json-schema/reference/regular_expressions.html
     * @param $string
     * @throws InvalidScalarValueException
     */
    protected function checkPattern($string)
    {
        if ($pattern = $this->schemaAccessor->getPattern()) {
            if (!preg_match('/'.$pattern.'/', $string)) {
                throw new InvalidScalarValueException('Validation of string failed regular expression in "pattern".');
            }
        }
    }

    /**
     * @param $string
     * @throws InvalidFormatException
     */
    protected function checkFormat($string)
    {
        if ($format = $this->schemaAccessor->getFormat()) {
            if ($this->container->make(FormatValidatorInterface::class)->check($format, $string) === false) {
                throw new InvalidFormatException('Value did not match format "'.$format.'"');
            }
        }
    }
}