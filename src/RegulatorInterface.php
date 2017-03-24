<?php

namespace Dto;

/**
 * Interface RegulatorInterface
 * The regulator is the gatekeeper: it controls which data is allowed to be stored and how it is to be stored.
 * @package Dto
 */
interface RegulatorInterface
{

    /**
     * Determines what kind of storage vessel the DTO will be: object, array, scalar
     * @param $value
     * @param array $schema
     * @return mixed
     */
    public function chooseDataStorageType($value, array $schema);

    /**
     * Required for certain validation keywords that need to compare the incoming value against what is already stored,
     * e.g. "uniqueItems", "maxItems", "maxProperties"
     * @param DtoInterface $dto
     */
    public function postValidate(DtoInterface $dto);

    /**
     * Validate/filter the given $value against the given $schema
     *
     * @param mixed $value
     * @param array $schema
     * @return mixed
     */
    public function rootFilter($value, array $schema = []);

    /**
     * Get the default value considering the $input value
     * @param mixed|null $input
     * @param array
     * @return mixed
     */
    public function getDefault($input = null, array $schema = []);

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
     * @param string $base_dir
     * @return array
     */
    public function compileSchema($schema = null, $base_dir = '');

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