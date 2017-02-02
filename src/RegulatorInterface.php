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
     * For getting the sub-schema corresponding to an array index
     * @param $index
     * @return array
     */
    public function getSchemaAtIndex($index);

    /**
     * For getting the sub-schema corresponding to an object key
     * @param $key string
     * @return array
     */
    public function getSchemaAtKey($key);

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

}