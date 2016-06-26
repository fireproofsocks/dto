<?php
namespace S6\DataTransfer\NodeType;

class IntegerNode extends ScalarNode
{
    /**
     * Appending integers not supported
     *
     * @param $dotted_key
     * @param $value
     * @param $key null
     * @return bool
     */
    public function append($dotted_key, $value, $key = null)
    {
        $this->dto->log(__FUNCTION__.'() not allowed in '.__CLASS__.' at key '.$dotted_key);
        return false;
    }

    public function set($dotted_key, $value)
    {
        $value = (int) $value;
        return parent::set($dotted_key, $value);
    }

    /**
     * splicing integers not supported.
     *
     * @param $dotted_key string
     * @param $value string
     * @param $offset integer
     * @return boolean
     */
    public function splice($dotted_key, $value, $offset)
    {
        $this->dto->log(__FUNCTION__.'() not allowed in '.__CLASS__.' at key '.$dotted_key);
        return false;
    }
}