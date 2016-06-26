<?php
namespace S6\DataTransfer\NodeType;
/**
 * The Enum nodes may contain a single scalar or a list of items from the defined list of items.
 */
use S6\Contracts\Dto\DtoContract;
use S6\Contracts\Dto\DtoNodeDefinitionContract;

class EnumNode implements DtoNodeDefinitionContract
{
    protected $dto;
    protected $config = [
        'items' => []
    ];

    public function __construct(DtoContract $dto, array $config)
    {
        if (!isset($config['items']) || !is_array($config['items']) || empty($config['items'])) {
            throw new \Exception('Enum definitions require "items" to be a non-empty array of allowed items.');
        }

        // Read configurable options (allows values from original $this->options only)
        $config = array_intersect_key($config, $this->config);
        $this->config = $config + $this->config;
        $this->dto = $dto;
    }

    public function append($dotted_key, $value, $key = null)
    {
        $location = &$this->dto->get($dotted_key);
        if (in_array($value, $this->config['items'])) {
            $location[] = $value;
            return true;
        }
        $this->dto->log(__FUNCTION__.'() unallowed value in '.__CLASS__.' at key '.$dotted_key);
        return false;
    }

    public function set($dotted_key, $value)
    {
        $location = &$this->dto->get($dotted_key);
        if (in_array($value, $this->config['items'])) {
            $location = $value;
            return true;
        }
        $this->dto->log(__FUNCTION__.'() unallowed value in '.__CLASS__.' at key '.$dotted_key);
        return false;
    }

    public function splice($dotted_key, $value, $offset)
    {
        $location = &$this->dto->get($dotted_key);

        // PHP will have a fatal error if you try to append to a string.
        // Technically, an empty string is a scalar, but PHP will allow appending to an empty string,
        // but that's splitting hairs.  Null values are not considered scalars.
        if (is_scalar($location)) {
            return false;
        }

        if (in_array($value, $this->config['items'])) {
            array_splice($location, $offset, 0, $value);
            return true;
        }
        $this->dto->log(__FUNCTION__.'() unallowed value in '.__CLASS__.' at key '.$dotted_key);
        return false;
    }
}