<?php

namespace Dto;

class Detector
{
    public function isObject($value)
    {
        return (is_object($value));
    }

    /**
     * Is True Array?
     *
     * Helps us work around one of PHP's warts: there are no true arrays in PHP.
     * http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
     * @param $value mixed
     * @return bool
     */
    public function isArray($value)
    {
        if (!is_array($value)) {
            return false;
        }

        if (empty($value)) {
            return true;
        }

        return array_keys($value) === range(0, count($value) - 1);
    }

    public function isString($value)
    {
        return (is_string($value));
    }

    public function isInteger($value)
    {
        return (is_integer($value));
    }

    public function isNumber($value)
    {
        // Fall back to integers
        return (is_float($value)) ? true : $this->isInteger($value);
    }

    public function isBoolean($value)
    {
        return (is_bool($value));
    }

    public function isNull($value)
    {
        return ($value === null);
    }
}