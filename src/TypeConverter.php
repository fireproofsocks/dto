<?php
namespace Dto;

class TypeConverter implements TypeConverterInterface
{

    public function toObject($value)
    {
        if (is_array($value)) {
            return (object) $value;
        }

        return (is_object($value)) ? $value : new \stdClass();
    }

    public function toArray($value)
    {
        if (is_object($value)) {
            $value = (array) $value;
        }

        return (is_array($value)) ? array_values($value) : [];
    }

    public function toString($value)
    {
        if (is_array($value)) {
            return '';
        }

        if (is_object($value)) {
            return (method_exists($value, '__toString')) ? strval($value) : '';
        }

        return strval($value);
    }

    public function toInteger($value)
    {
        return intval($this->toString($value));
    }

    public function toNumber($value)
    {
        return floatval($this->toString($value));
    }

    public function toBoolean($value)
    {
        if (is_array($value)) {
            return (empty($value)) ? false : true;
        }

        if (is_object($value)) {
            // Yes, an empty object is considered false here (unlike in standard PHP)
            return (empty((array) $value)) ? false : true;
        }
        // TODO: support for coercing boolean-like strings e.g. "Off", "On", "Yes", "No" etc.
        return boolval($value);
    }

    public function toNull($value)
    {
        return null;
    }

}