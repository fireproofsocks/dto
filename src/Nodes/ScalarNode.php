<?php
namespace S6\DataTransfer\NodeType;

use S6\Contracts\Dto\DtoContract;
use S6\Contracts\Dto\DtoNodeDefinitionContract;

class ScalarNode implements DtoNodeDefinitionContract
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

    /**
     * Appending scalars means concatenation.
     *
     * @param $dotted_key
     * @param $value
     * @param $key null (ignored)
     * @return bool
     */
    public function append($dotted_key, $value, $key = null)
    {
        $location = &$this->dto->get($dotted_key);

        if (is_array($location) || !is_scalar($value)) {
            return false;
        }
        $location = $location . $value;
        return true;
    }

    public function set($dotted_key, $value)
    {
        if (is_array($value)) {
            return false;
        }
        $location = &$this->dto->get($dotted_key);
        if (is_array($location)) {
            return false;
        }
        $location = $value;
        return true;
    }

    /**
     * Akin to concatenation, splicing into a scalar (string) means we're working with sub-strings
     * @param $dotted_key string
     * @param $value string
     * @param $offset integer
     * @return boolean
     */
    public function splice($dotted_key, $value, $offset)
    {
        $location = &$this->dto->get($dotted_key);
        if (is_array($location) || !is_scalar($value)) {
            return false;
        }
        $prefix = substr($location, 0, $offset);
        $suffix = substr($location, $offset);
        $location = $prefix . $value . $suffix;

        return true;
    }
}