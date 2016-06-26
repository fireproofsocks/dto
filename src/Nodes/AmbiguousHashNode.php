<?php
namespace S6\DataTransfer\NodeType;
/**
 * Class AmbiguousHashNode
 *
 * This is a special case for allowing key/value pairs to populate a node without knowing what the keys are beforehand.
 *
 * @package S6\DataTransfer\NodeType
 */
class AmbiguousHashNode extends HashNode
{
    /**
     * Unlike inferred HashNodes, we don't care what data type is at the $location (because we have explicitly said we
     * are treating it as a hash), and we only want to use hashes as $values.  The behavior is to merge the hashes.
     *
     * @param string $dotted_key
     * @param mixed $value
     * @param $key null
     * @return mixed
     */
    public function append($dotted_key, $value, $key = null)
    {
        // We only want to handle hash values
        if (!$this->dto->isHash($value)) {
            $this->dto->log('Ambiguous hash node requires hash value when setting or appending to location: '.$dotted_key, 'error');
            return false;
        }

        $location = &$this->dto->get($dotted_key);

        if (!is_array($location)) {
            $location = $value;
        }
        else {
            $location = array_merge($location, $value);
        }

        return true;
    }

    /**
     * Alias for append.
     *
     * @param string $dotted_key
     * @param mixed $value
     * @return bool
     */
    public function set($dotted_key, $value)
    {
        return $this->append($dotted_key, $value);
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