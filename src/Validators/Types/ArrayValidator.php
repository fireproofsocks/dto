<?php
namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidArrayValueException;
use Dto\Exceptions\InvalidDataTypeException;
use Dto\JsonSchemaAccessorInterface;
use Dto\TypeDetectorInterface;
use Dto\Validators\AbstractValidator;
use Dto\Validators\ValidatorInterface;

class ArrayValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * maxItems, minItems, uniqueItems
     * @param $value mixed
     * @param $schema array
     * @return mixed
     */
    public function validate($value, array $schema)
    {
        $this->schemaAccessor = $this->container->make(JsonSchemaAccessorInterface::class)->factory($schema);

        $this->checkDataType($value);
        $this->checkMaxItems($value);
        $this->checkMinItems($value);
        $this->checkUniqueItems($value);

        return $value;
    }

    protected function checkDataType($value)
    {
        if (!$this->container->make(TypeDetectorInterface::class)->isArray($value)) {
            throw new InvalidDataTypeException('Value is not a true array.');
        }
    }

    /**
     * NOTE: this only checks the condition of the value being added.  Some checks need to be done in preValidation to
     * compare them against the values that have already been stored.
     * @param $value
     * @throws InvalidArrayValueException
     */
    protected function checkMaxItems($value)
    {
        $max = $this->schemaAccessor->getMaxItems();
        if ($max !== false) {
            if (count($value) > $max) {
                throw new InvalidArrayValueException('Arrays with more than '.$max.' items disallowed by "maxItems".');
            }
        }
    }

    /**
     * NOTE: this only checks the condition of the value being added.  Some checks need to be done in preValidation to
     * compare them against the values that have already been stored.
     * @param $value
     * @throws InvalidArrayValueException
     */
    protected function checkMinItems($value)
    {
        $min = $this->schemaAccessor->getMinItems();
        if ($min !== false) {
            if (count($value) < $min) {
                throw new InvalidArrayValueException('Arrays with fewer than '.$min.' items disallowed by "minItems".');
            }
        }
    }

    /**
     * NOTE: this only checks the condition of the value being added.  Some checks need to be done in preValidation to
     * compare them against the values that have already been stored.
     * @param $value
     * @throws InvalidArrayValueException
     */
    protected function checkUniqueItems($value)
    {
        if ($this->schemaAccessor->getUniqueItems()) {
            if (count($value) !== count(array_unique($value))) {
                throw new InvalidArrayValueException('Arrays with duplicate values are not allowed when "uniqueItems" is true.');
            }
        }
    }
}