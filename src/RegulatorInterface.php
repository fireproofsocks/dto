<?php

namespace Dto;

interface RegulatorInterface
{
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
     * Set the regulator schema
     * @param mixed|null $schema
     * @return mixed
     */
    public function setSchema($schema = null);

    /**
     * @param mixed $value
     * @return mixed
     */
    public function validate($value);

}