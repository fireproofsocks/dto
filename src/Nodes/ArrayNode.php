<?php
namespace S6\DataTransfer\NodeType;

/**
 * An Array node contains a simple list of items
 */
use S6\Contracts\Dto\DtoContract;
use S6\Contracts\Dto\DtoNodeDefinitionContract;

class ArrayNode implements DtoNodeDefinitionContract
{
    protected $dto;
    protected $config = [];

    public function __construct(DtoContract $dto, array $config)
    {
        // Read configurable options (allows values from original $this->options only)
        $config = array_intersect_key($config, $this->config);
        $this->config = $config + $this->config;
        $this->dto = $dto;
    }

    public function append($dotted_key, $value, $key = null)
    {
        $location = &$this->dto->get($dotted_key);

        // PHP will have a fatal error if you try to append to a string.
        // Technically, an empty string is a scalar, but PHP will allow appending to an empty string,
        // but that's splitting hairs.  Null values are not considered scalars.
        if (is_scalar($location)) {
            return false;
        }

        // If your value is an array, we loop through it and append the component parts
        // This may cause problems if you want to store an array of arrays... but arguably that's not what a DTO
        // is good for.
        if (is_array($value)) {
            foreach ($value as $v) {
                $location[] = $v;
            }
            return true;
        }

        $location[] = $value;

        return true;
    }

    public function set($dotted_key, $value)
    {
        // You should only be able to replace an array (@ $location) with another array
        if (!is_array($value)) {
            return false;
        }

        $location = &$this->dto->get($dotted_key);
        $location = $value;
        return true;
    }

    public function splice($dotted_key, $value, $offset)
    {
        $location = &$this->dto->get($dotted_key);

        // PHP will have a fatal error if you try to push an element onto a variable that contains a non-empty string.
        // You can append a value to an empty string, and it will become an array.  So we could check if the $location
        // is a scalar AND non-empty, but the more expected behavior is to simply fail if the variable is not an array.
        if (!is_array($location)) {
            return false;
        }

        array_splice($location, $offset, 0, $value);
        return true;

    }
}