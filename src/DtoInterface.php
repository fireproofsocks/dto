<?php

namespace Dto;

interface DtoInterface
{
    /**
     * Populate the DTO.
     * @param $value mixed
     */
    public function hydrate($value);

    /**
     * Get the value at the given key (for objects) or at the given index (for arrays)
     * @param $index mixed string | integer
     * @return mixed
     */
    public function get($index);

    /**
     * Set a property on an object (objects only)
     * @param $key string
     * @param $value mixed
     * @return void
     */
    public function set($key, $value);

    /**
     * Unset the value at the given integer index (for arrays) or at the given string key (for objects)
     * @param $index mixed
     * @return void
     */
    public function forget($index);

    /**
     * Check whether the given integer index exists (for arrays) or the given string key (for objects)
     * @param $index mixed
     * @return boolean
     */
    public function exists($index);

    /**
     * Get the defined JSON Schema for the DTO as a PHP associative array
     * @return array
     */
    public function getSchema();

    /**
     * Return the DTO contents as a stdClass object. This operation is intended for objects, but it can be used to
     * represent PHP arrays since they are not true arrays.  This operation will fail for scalar DTOs.
     * @return object
     */
    public function toObject();

    /**
     * Return the DTO contents as PHP array. This operation will fail for scalar DTOs.
     * @return array
     */
    public function toArray();

    /**
     * Return the DTO contents as a JSON string
     * @return string
     */
    public function toJson();

    /**
     * Return the DTO contents as a scalar value (e.g. string or integer).  This operation will fail for aggregate DTOs
     * (arrays or objects).
     * @return mixed
     */
    public function toScalar();

    /**
     * Returns scalar | array | object -- indicates the internal storage type used for the DTO
     * @return string
     */
    public function getStorageType();

    /**
     * Returns the base directory used for resolving any relative paths to schemas (indicated with the $ref keyword).
     * @return string
     */
    public function getBaseDir();
}