<?php
namespace S6\DataTransfer\NodeType;
/**
 * Class HashNode
 *
 * Hashes are the backbone of the DTOs.
 * Hash nodes' main job is to loop back to fromArray() in order to traverse down the structure.
 *
 * @package S6\DataTransfer\NodeType
 */
class HashNode extends ArrayNode
{
    public function append($dotted_key, $value, $key = null)
    {
        $location = &$this->dto->get($dotted_key);

        if (is_scalar($location) || !is_array($value)) {
            return false;
        }

        $location = array_merge($location, $value);

        return true;
    }

    /**
     * Hashes map back back to the parent.
     * @param string $dotted_key
     * @param mixed $value
     * @return mixed
     */
    public function set($dotted_key, $value)
    {
        if (!is_array($value)) {
            // Flow shouldn't direct here (except in isolated tests)
            return $this->dto->set($dotted_key, $value);
        }
        $this->dto->log(__CLASS__.'::'.__FUNCTION__ . '(): raw key: '.$dotted_key, 'info');
        foreach ($value as $k => $v) {

            $node_key = trim(trim($dotted_key, '.') . '.' . $k, '.');

            try {
                $this->dto->get($node_key);
            } catch (\Exception $e) {
                // skip that node and its children
                $this->dto->log(__CLASS__.'::'.__FUNCTION__ . '(): key "' . $node_key . '" undefined in data template, skipping it and its children', 'info');
                continue;
            }

            $this->dto->set($node_key, $v);
        }
        return true;
        //return $this->dto->fromArray($value, $dotted_key);
    }

    /**
     * Unsupported.  Since hashes are unordered, there's no way to splice into one.
     *
     * @param $dotted_key
     * @param $value
     * @param $offset
     * @return bool
     */
    public function splice($dotted_key, $value, $offset)
    {
        return false;
    }
}