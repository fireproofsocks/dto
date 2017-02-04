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
        $this->validateProperties($value);

        return true;
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

    protected function validateProperties($object)
    {
        $properties = $this->schemaAccessor->getProperties();

        foreach ($object as $k => $v) {
            if (array_key_exists($k, $properties)) {
                // $properties[$k];
                // ValidatorSelectorInterface :: selectValidators($schema)
                // RegulatorInterface -> filter
                // TODO: compile and dereference?!
                //print __LINE__; print_r($properties[$k]); exit;
                $schema = $this->container[RegulatorInterface::class]->compileSchema($properties[$k]);
                $this->container[RegulatorInterface::class]->filter($v, $schema);
            }
        }
    }
}