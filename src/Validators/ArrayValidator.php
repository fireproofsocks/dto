<?php
namespace Dto\Validators;

use Dto\Exceptions\InvalidArrayValueException;

class ArrayValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * maxItems, minItems, uniqueItems
     * @param $value
     * @return boolean
     */
    public function validate($value)
    {
        $this->checkMaxItems($value);
        $this->checkMinItems($value);
        $this->checkUniqueItems($value);

        return true;
    }

    protected function checkMaxItems($value)
    {
        $max = $this->schema->getMaxItems();
        if ($max !== false) {
            if (count($value) > $max) {
                throw new InvalidArrayValueException('Arrays with more than '.$max.' items disallowed by "maxItems".');
            }
        }
    }

    protected function checkMinItems($value)
    {
        $min = $this->schema->getMinItems();
        if ($min !== false) {
            if (count($value) < $min) {
                throw new InvalidArrayValueException('Arrays with fewer than '.$min.' items disallowed by "minItems".');
            }
        }
    }

    protected function checkUniqueItems($value)
    {
        if ($this->schema->getUniqueItems()) {
            if (count($value) !== count(array_unique($value))) {
                throw new InvalidArrayValueException('Arrays with duplicate values are not allowed when "uniqueItems" is true.');
            }
        }
    }
}