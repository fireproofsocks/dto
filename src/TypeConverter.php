<?php
namespace Dto;

/**
 * Class TypeConverter
 *
 * When JSON Schema refers to data of a certain "type", this handles the conversion of that data to what the application
 * works with internally.  E.g. an "object" is not a handled as a PHP stdClass object, but as an associative array.
 *
 * @package Dto
 */
class TypeConverter implements TypeConverterInterface
{

    public function toObject($value)
    {
        if (is_object($value)) {
            $value = (array) $value;
        }

        return (is_array($value)) ? $value : [];

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