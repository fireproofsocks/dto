<?php

namespace Dto;

interface RegulatorInterface
{
    /**
     * Get the schema (as array) for the given $propertyName (a null value indicates the root schema).
     * Some property names are not valid because of constraints defined in "properties", "patternProperties", or in
     * "additionalProperties".
     *
     * @link http://json-schema.org/example2.html
     * @link https://spacetelescope.github.io/understanding-json-schema/reference/object.html
     * @param $propertyName string
     * @return array
     */
    public function getSchemaArray($propertyName = null);

    /**
     * Get the data type possible for the given property.
     * This is where we evaluate properties, patternProperties, and additionalProperties.  It is necessary to know which
     * data type to use for storage because multiple schemas may be possible. This method lets us know if the given
     * property index name can be used at all, and if so, how to treat the data at the location indicated.
     * @link http://json-schema.org/example2.html
     * @link https://spacetelescope.github.io/understanding-json-schema/reference/object.html
     * @param $name string
     * @return mixed string on success boolean false on fail
     */
    //public function getSchemaByPropertyName($name);

    /**
     * Is the entity described by the root level schema an object?
     * @return boolean
     */
    public function isObject();

    /**
     * Is the entity described by the root level schema an arraya?
     * @return boolean
     */
    public function isArray();

    /**
     * Is the entity described by the root level schema a scalar (string, integer, number, boolean, or null)?
     * @return boolean
     */
    public function isScalar();

    /**
     * Does is the given object valid
     * @param $object object
     * @return mixed
     */
    // TODO???
//    public function isValidObject($object);
//
//    public function isValidArray($array);

    /**
     * Considers the following keywords:
     *      for numbers (integers?): multipleOf, maximum, exclusiveMaximum, minimum, exclusiveMinimum
     *      for strings: maxLength, minLength, pattern
     *
     * Should pattern apply also to numbers?
     *
     * The given $scalar must be properly type-cast BEFORE being sent to this function.
     *
     * @param $scalar mixed
     * @return boolean
     */
    public function checkValidScalar($scalar);

    /**
     * Return the data type that the given $value can be stored as. Is the given $value type storable given the current root schema?
     * A value is storable when it is detected to be of type X and type X is defined as the schema's type or included
     * in the schema's list of allowed types.
     * @param $value mixed
     * @return mixed string data type on success, boolean false on fail
     */
    public function getStorableTypeByValue($value);

    /**
     * Get the defined data type(s).
     * @return mixed string | array
     */
    public function getType();

}