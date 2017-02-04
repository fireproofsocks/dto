<?php

namespace Dto;

interface RegulatorInterface
{
    /**
     * Validate/filter the given $value against the given $schema
     *
     * @param mixed $value
     * @param array $schema
     * @return mixed
     */
    public function filter($value, array $schema = []);

    /**
     * Get the default value considering the $input value
     * @param mixed|null $input
     * @return mixed
     */
    public function getDefault($input = null);

    /**
     * Is the entity being regulated an object?
     * @return boolean
     */
    public function isObject();

    /**
     * Is the entity being regulated an array?
     * @return boolean
     */
    public function isArray();

    /**
     * Is the entity being regulated a scalar (string, integer, number, boolean, or null)?
     * @return boolean
     */
    public function isScalar();

    /**
     * Resolve schema references and return the compiled array
     * @param mixed|null $schema
     * @return array
     */
    public function compileSchema($schema = null);

    /**
     * return the $value validated/filtered for the given $index in the array
     * @param $value
     * @param $index
     * @param $schema array
     * @return mixed
     */
    public function getFilteredValueForIndex($value, $index, array $schema);

    /**
     * return the $value validated/filtered for the given $key in the object
     * @param $value
     * @param $key
     * @param $schema array
     * @return mixed
     */
    public function getFilteredValueForKey($value, $key, array $schema);

    /**
     * @param $value
     * @param $schema
     * @return mixed
     */
    public function filterArray($value, $schema);

    /**
     * @param $value
     * @param $schema
     * @return mixed
     */
    public function filterObject($value, $schema);

}