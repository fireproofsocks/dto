<?php
namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidObjectValueException;
use Dto\JsonSchemaAccessorInterface;
use Dto\RegulatorInterface;
use Dto\TypeDetectorInterface;
use Dto\Validators\AbstractValidator;
use Dto\Validators\ValidatorInterface;

class ObjectValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * maxProperties, minProperties
     * @param $value
     * @param $schema array
     * @return boolean
     */
    public function validate($value, array $schema)
    {
        $this->schemaAccessor = $this->container[JsonSchemaAccessorInterface::class]->load($schema);

        $this->checkDataType($value);
        $this->checkMaxProperties($value);
        $this->checkMinProperties($value);
        $this->checkRequired($value);

        return $this->validateProperties($value, $schema);
    }

    protected function checkDataType($value)
    {
        if (!$this->container[TypeDetectorInterface::class]->isObject($value)) {
            throw new InvalidObjectValueException('Value is not an object.');
        }
    }

    protected function checkMaxProperties($value)
    {
        $max = $this->schemaAccessor->getMaxProperties();
        if ($max !== false) {
            if (count($value) > $max) {
                throw new InvalidObjectValueException('Objects with more than '.$max.' properties disallowed by "maxProperties".');
            }
        }
    }

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

    protected function validateProperties($object, $schema)
    {
        foreach ($object as $k => $v) {
            $object[$k] = $this->container[RegulatorInterface::class]->getFilteredValueForKey($v, $k, $schema);
        }

        return $object;
    }
}