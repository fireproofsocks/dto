<?php
namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidObjectValueException;
use Dto\JsonSchemaAccessorInterface;
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

        $this->checkMaxProperties($value);
        $this->checkMinProperties($value);

        return true;
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
}