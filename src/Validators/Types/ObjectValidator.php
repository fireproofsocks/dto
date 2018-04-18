<?php
namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidDataTypeException;
use Dto\Exceptions\InvalidObjectValueException;
use Dto\JsonSchemaAccessorInterface;
use Dto\TypeDetectorInterface;
use Dto\Validators\AbstractValidator;
use Dto\Validators\ValidatorInterface;

class ObjectValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * maxProperties, minProperties
     * @param $value
     * @param $schema array
     * @return mixed
     */
    public function validate($value, array $schema)
    {
        $this->schemaAccessor = $this->container->make(JsonSchemaAccessorInterface::class)->factory($schema);

        $this->checkDataType($value);
        $this->checkMaxProperties($value);
        $this->checkMinProperties($value);
        $this->checkRequired($value);

        return $value;
    }

    protected function checkDataType($value)
    {
        if (!$this->container->make(TypeDetectorInterface::class)->isObject($value)) {
            throw new InvalidDataTypeException('Value is not an object.');
        }
    }

    /**
     * NOTE: this only checks the condition of the value being added.  Some checks need to be done in preValidation to
     * compare them against the values that have already been stored.
     * @param $value
     * @throws InvalidObjectValueException
     */
    protected function checkMaxProperties($value)
    {
        $max = $this->schemaAccessor->getMaxProperties();
        if ($max !== false) {
            if (count($value) > $max) {
                throw new InvalidObjectValueException('Objects with more than '.$max.' properties disallowed by "maxProperties".');
            }
        }
    }

    /**
     * NOTE: this only checks the condition of the value being added.  Some checks need to be done in preValidation to
     * compare them against the values that have already been stored.
     * @param $value
     * @throws InvalidObjectValueException
     */
    protected function checkMinProperties($value)
    {
        $min = $this->schemaAccessor->getMinProperties();
        if ($min !== false) {
            if (count($value) < $min) {
                throw new InvalidObjectValueException('Objects with fewer than '.$min.' properties disallowed by "minProperties".');
            }
        }
    }

    protected function checkRequired($value)
    {
        $required = $this->schemaAccessor->getRequired();
        foreach ($required as $r) {
            if (!array_key_exists($r, $value)) {
                throw new InvalidObjectValueException('Object is missing required value: '. $r);
            }
        }
    }
}