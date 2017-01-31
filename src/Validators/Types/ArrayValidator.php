<?php
namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidArrayValueException;
use Dto\JsonSchemaAccessorInterface;
use Dto\Validators\AbstractValidator;
use Dto\Validators\ValidatorInterface;

class ArrayValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * maxItems, minItems, uniqueItems
     * @param $value mixed
     * @param $schema array
     * @return boolean
     */
    public function validate($value, array $schema)
    {
        $this->schemaAccessor = $this->container[JsonSchemaAccessorInterface::class]->load($schema);

        $this->checkMaxItems($value);
        $this->checkMinItems($value);
        $this->checkUniqueItems($value);

        return true;
    }

    protected function checkMaxItems($value)
    {
        $max = $this->schemaAccessor->getMaxItems();
        if ($max !== false) {
            if (count($value) > $max) {
                throw new InvalidArrayValueException('Arrays with more than '.$max.' items disallowed by "maxItems".');
            }
        }
    }

    protected function checkMinItems($value)
    {
        $min = $this->schemaAccessor->getMinItems();
        if ($min !== false) {
            if (count($value) < $min) {
                throw new InvalidArrayValueException('Arrays with fewer than '.$min.' items disallowed by "minItems".');
            }
        }
    }

    protected function checkUniqueItems($value)
    {
        if ($this->schemaAccessor->getUniqueItems()) {
            if (count($value) !== count(array_unique($value))) {
                throw new InvalidArrayValueException('Arrays with duplicate values are not allowed when "uniqueItems" is true.');
            }
        }
    }
}