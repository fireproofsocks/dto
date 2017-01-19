<?php
namespace Dto\Validators;

use Dto\Exceptions\InvalidObjectValueException;

class ObjectValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * maxProperties, minProperties
     * @param $value
     * @return boolean
     */
    public function validate($value)
    {
        $this->checkMaxProperties($value);
        $this->checkMinProperties($value);

        return true;
    }

    protected function checkMaxProperties($value)
    {
        $max = $this->schema->getMaxProperties();
        if ($max !== false) {
            if (count($value) > $max) {
                throw new InvalidObjectValueException('Objects with more than '.$max.' properties disallowed by "maxProperties".');
            }
        }
    }

    protected function checkMinProperties($value)
    {
        $min = $this->schema->getMinProperties();
        if ($min !== false) {
            if (count($value) < $min) {
                throw new InvalidObjectValueException('Objects with fewer than '.$min.' properties disallowed by "minProperties".');
            }
        }
    }
}