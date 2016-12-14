<?php

namespace Dto;

use Dto\Exceptions\AppendException;
use Dto\Exceptions\InvalidDataTypeException;
use Dto\Exceptions\InvalidLocationException;
use Dto\Exceptions\InvalidMetaKeyException;
use Dto\Exceptions\InvalidMutatorException;

/**
 * Class Dto (Data Transfer Object).
 *
 * Allows object schemas to be defined and helps to normalize object access.
 *
 * See http://php.net/manual/en/class.arrayobject.php, some ideas from
 * https://symfony.com/doc/current/components/property_access/introduction.html#installation
 * others from http://json-schema.org/
 *
 * @category
 */
class Dto extends \ArrayObject
{
    protected $template = [];
    protected $meta = [];
    
    /**
     * Dto constructor.
     *
     * @param array $input values filtered against the $template and $meta
     * @param array $template template (i.e. default values) with loosely typed values
     * @param array $meta extra info about the template data.
     */
    public function __construct(
        array $input = [],
        array $template = [],
        array $meta = []
    ) {
        $this->setFlags(0);
        $arg_list = func_get_args();
        
        // We need to be able to override the class variables when the input variables are empty.
        $this->template = (isset($arg_list[1])) ? $arg_list[1] : $this->template;
        $this->meta = (isset($arg_list[2])) ? $arg_list[2] : $this->meta;
        $this->meta = $this->autoDetectTypes($this->template,
            $this->normalizeMeta($this->meta));
        // We must always ensure that the template's properties are passed to filterRoot
        $input = array_replace_recursive($this->template, $input);
        // store the filtered values in the ArrayObject
        parent::__construct($this->filterRoot($input));
    }
    
    /**
     * Normalize the keys used to define meta data.  For dev's UX, keys like "x" are
     * allowed in the template definitions, but internally, we consider all locations
     * defined in the meta definition to begin with "." -- this normalizes the keys
     * of the given $meta array to use the leading dot format.  Exceptions are raised
     * for invalid keys.
     *
     * @param $meta array hash of meta definitions.
     *
     * @return array
     *
     * @throws InvalidMetaKeyException
     */
    protected function normalizeMeta(array $meta)
    {
        $normalized = [];
        foreach ($meta as $key => $value) {
            $key = $this->getNormalizedKey($key);
            if (!$this->isValidMetaKey($key)) {
                throw new InvalidMetaKeyException('The key "' . $key . '" contains invalid characters or points to an invalid location."');
            }
            
            $normalized[$key] = $value;
        }
        
        return $normalized;
    }
    
    /**
     * Gets the official "normalized" fully-qualified version of the dotted-key, which begin with a leading dot.
     *
     * @param $key
     *
     * @return string
     */
    protected function getNormalizedKey($key)
    {
        return '.' . trim($key, '.');
    }
    
    /**
     * @param $key
     *
     * @return bool
     */
    protected function isValidMetaKey($key)
    {
        if (!is_scalar($key)) {
            return false;
        }
        
        if (empty($key)) {
            return false;
        }
        if (strpos($key, '..') !== false) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Fill in empty spots in the $this->meta definitions based on what is provided
     * in the $template.  This function only operates on the first "left-most" (i.e.
     * top-level) nodes: deeper structures will be iteratively passed to child
     * instances.  For instance, $template = ['is_on' => false] would have the same
     * effect as explicitly defining the corresponding $meta key as a 'boolean' type:
     *
     *      $meta = ['is_on' => ['type => 'boolean']];.
     *
     * Modifies class-level $this->meta.
     *
     * @param array $template
     * @param array $meta (normalized)
     *
     * @return array
     *
     * @throws InvalidDataTypeException
     */
    protected function autoDetectTypes($template, $meta)
    {
        foreach ($template as $index => $v) {
            $meta_key = $this->getNormalizedKey($index);
            $meta[$meta_key]['nullable'] = (isset($meta[$meta_key]['nullable'])) ? $meta[$meta_key]['nullable'] : false;
            
            if (isset($meta[$meta_key]['type'])) {
                if (!$this->isScalarType($meta[$meta_key]['type'])) {
                    if (!isset($meta[$meta_key]['values']['type'])) {
                        $meta[$meta_key]['values']['type'] = 'unknown';
                    }
                }
                continue; // skip the rest if type is already set
            }
            
            if (!is_array($template[$index])) {
                if (is_bool($template[$index])) {
                    $meta[$meta_key]['type'] = 'boolean';
                } elseif (is_int($template[$index])) {
                    $meta[$meta_key]['type'] = 'integer';
                } elseif (is_numeric($template[$index])) {
                    $meta[$meta_key]['type'] = 'float';
                } elseif (is_scalar($template[$index])) {
                    $meta[$meta_key]['type'] = 'scalar';
                }
            } // Hashes
            else {
                $meta[$meta_key]['type'] = ($this->isHash($template[$index])) ? 'hash' : 'array';
                $meta[$meta_key]['values']['type'] = 'unknown';
            }
        }
        
        // Declare the basic object type of the root node (i.e. THIS ArrayObject)
        if (empty($template)) {
            if (!isset($meta['.']['type'])) {
                $meta['.']['type'] = 'hash';
            }
            if (!isset($meta['.']['values']['type'])) {
                $meta['.']['values']['type'] = 'unknown';
            }
        }
        
        return $meta;
    }
    
    /**
     * Determines if the given $type is a scalar type (i.e. one intended to hold a single value) vs. a "composite" type,
     * i.e. one designed to hold multiple values.
     *
     * @param $type string
     *
     * @return bool
     */
    protected function isScalarType($type)
    {
        return !in_array($type, ['array', 'hash', 'dto']);
    }
    
    /**
     * Is the given var a hash (associative array)?
     *
     * See http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
     *
     * @param $arr mixed
     *
     * @return bool
     */
    public function isHash($arr)
    {
        if (!is_array($arr) || empty($arr)) {
            return false;
        }
        
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    
    /**
     * Special case for setting the root node: we need to loop over individual keys.
     *
     * @param $value mixed
     * @throws InvalidDataTypeException
     * @return mixed
     */
    protected function filterRoot($value)
    {
        foreach ($value as $k => $v) {
            $child_index = '.' . $k;
            try {
                $value[$k] = $this->filterNode($v, $child_index);
            } catch (\Exception $e) {
                $this->handleException($e, $k, $v);
                unset($value[$k]);
            }
        }
        
        // The root object is assumed to be a hash unless defined otherwise
        return ((isset($this->meta['.']['type']) && $this->meta['.']['type'] == 'array')) ? array_values($value) : $value;
    }
    
    /**
     * Override this function in a child class if needed (see the DtoStrict class).
     * The method is implemented for "graceful/silent failing".
     * @param \Exception $e
     * @param string $index the location being written to
     * @param mixed $value the problematic value
     * @throws InvalidDataTypeException
     */
    protected function handleException(\Exception $e, $index, $value)
    {
        if ($e instanceof InvalidDataTypeException) {
            throw $e;
        }
    }
    
    /**
     * Ensure that it's legit to write to the given $index (a 1st level location).
     * For append operations, the $index will be null.
     *
     * @param $index mixed (null for appends, string for set operations)
     * @param $template array
     *
     * @return bool
     */
    protected function isValidTargetLocation($index, array $template)
    {
        if (empty($template) || is_null($index)) {
            return true;
        }
    
        
        $parent_index = $this->getParentIndex($this->getNormalizedKey($index));
        $meta = $this->getMeta($parent_index);
        
        if (isset($meta['ambiguous']) && $meta['ambiguous']) {
            return true;
        }
        
        // Make sure the target location exists
        return array_key_exists(trim($index, '.'), $template);
    }
    
    /**
     * Ensure that it's kosher to store the given $value at the given location $index.
     * This is where we prevent scalars from overwriting arrays or vice versa.
     *
     * @param $value
     * @param $index
     * @return bool
     *
     * @throws InvalidDataTypeException
     */
    protected function isValidMapping($value, $index)
    {
        
        // Check for compatible value/target
        $normalized_key = $this->getNormalizedKey($index);
        $meta = $this->getMeta($index);
        
        // Append operation
        if (is_null($index)) {
            $target_type = $meta['values']['type'];
        } else {
            $target_type = (isset($meta['type'])) ? $meta['type'] : 'unknown';
        }
        
        if ($target_type == 'unknown') {
            return true;
        } // Scalar Types
        elseif ($this->isScalarType($target_type)) {
            if (is_scalar($value) || is_null($value)) {
                return true;
            }
            throw new InvalidDataTypeException('Cannot write non-scalar value to scalar location "' . $normalized_key . '"');
        } // Composite Types
        else {
            if (is_scalar($value) && $value != null) {
                throw new InvalidDataTypeException('Cannot write scalar value to non-scalar location "' . $normalized_key . '"');
            }
        }
        
        return true;
    }
    
    /**
     * Get the meta definition data for the given index (normalized or not).
     *
     * @param $index string
     *
     * @return array (associative) guaranteed to have keys for "type" and "values"
     */
    protected function getMeta($index)
    {
        $normalized_key = $this->getNormalizedKey($index);

        if (!isset($this->meta[$normalized_key])) {
            // if $normalized_key like ".0" -- i.e. array members
            if (is_numeric(trim($normalized_key, '.')) && isset($this->meta['.']['values'])) {
                return $this->meta['.']['values'];
            }
            // Or if it's an anonymous hash
            if (isset($this->meta['.']['anonymous']) && $this->meta['.']['anonymous'] && isset($this->meta['.']['values'])) {
                return $this->meta['.']['values'];
            }

            return ['type' => 'unknown']; // TODO: throw exception?
        }
        // TODO: Enforce some keys
        // Warning: this is dangerous because any properties on $this are interpreted as
        // values on the array we're trying to describe.
        //$this->meta[$normalized_key]['type'] = (isset($this->meta[$normalized_key]['type'])) ? $this->meta[$normalized_key]['type'] : 'unknown';
        //$meta = $this->meta[$normalized_key];
        //$meta[$normalized_key]['type'] = (isset($meta[$normalized_key]['type'])) ? $meta[$normalized_key]['type'] : 'unknown';
        //return $meta;
        return $this->meta[$normalized_key];
    }
    
    /**
     * Filter the incoming $value by finding and applying a mutator at the $index location specified.
     *
     * @param $value mixed
     * @param $index string
     *
     * @return mixed
     *
     * @throws InvalidDataTypeException
     * @throws InvalidLocationException
     */
    protected function filterNode($value, $index)
    {
        // Test for root-level arrays and index is null?
        if ($index === '.') {
            throw new InvalidLocationException('filterRoot must be used for root key (.)');
        }
        
        $normalized_key = $this->getNormalizedKey($index);

        if (!$this->isValidTargetLocation($index, $this->template)) {
            throw new InvalidLocationException('Index "' . $normalized_key . '" not valid for writing');
        }

        // Todo: let this exception be thrown
        if (!$this->isValidMapping($value, $index)) {
            throw new InvalidLocationException('Invalid mapping at "' . $normalized_key . '"');
        }
    
        // We can bypass a lot of logic if we are setting null on a nullable field
        if (is_null($value) && $this->isNullable($index)) {
            return null;
        }
        
        $mutatorFunction = $this->getMutator($value, $index);

        $value = $this->{$mutatorFunction}($value, $index, $this->getMeta($index));
        
        // Final gatekeeping: make sure our mutators don't try to sneak invalid values past
        if (!$this->isValidValue($value)) {
            throw new InvalidDataTypeException('Invalid data type cannot be written at location "' . $normalized_key . '"');
        }
        
        return $value;
    }
    
    /**
     * Contains the logic for resolving which mutator function to use.  It can be a
     * bit tricky when data is not explicitly defined (i.e. type=unknown).
     *
     * @param $value mixed
     * @param $index string
     *
     * @return string
     */
    protected function getMutator($value, $index)
    {
        if ($index == null) {
            return $this->getValueMutator($index);
        }
        
        $meta = $this->getMeta($index);
        $meta['type'] = (isset($meta['type'])) ? $meta['type'] : 'unknown';

        // Unknown can be either/or -- it changes depending on the value type
        if ($meta['type'] == 'unknown') {
            return (is_scalar($value) || is_null($value)) ? $this->getValueMutator($index) : $this->getCompositeMutator($index);
        }
        
        return ($this->isScalarType($meta['type'])) ? $this->getValueMutator($index) : $this->getCompositeMutator($index);
    }
    
    /**
     * Returns mutator function name used to mutate scalar values at the given $index
     * Type-mutator-methods use a prefix of "mutateType"; field-mutators use "mutate".
     *
     * @param $index (non-normalized)
     *
     * @return string function name
     *
     * @throws InvalidMutatorException
     */
    protected function getValueMutator($index)
    {
        $normalized_key = $this->getNormalizedKey($index);
        
        $meta = $this->getMeta($index);
        
        // Mutator specified for this specific Field-level
        if ($normalized_key != '.') {
            if (isset($meta['mutator'])) {
                if (method_exists($this, $meta['mutator'])) {
                    return $meta['mutator'];
                }
                throw new InvalidMutatorException('Mutator method "' . $meta['mutator'] . '"does not exist.  Defined at index "' . $normalized_key . '"');
            }
        }
        // This will always work if type defaults to "unknown"
        // Type-level Mutator
        if (isset($this->meta[$normalized_key]['type']) && $this->isScalarType($this->meta[$normalized_key]['type'])) {
            $functionName = $this->getFunctionName('mutateType',
                $this->meta[$normalized_key]['type']);
            if (!method_exists($this, $functionName)) {
                throw new InvalidMutatorException('Mutator method "' . $functionName . '"does not exist. Type defined in meta at index "' . $normalized_key . '"');
            }
            return $functionName;
        }
        // Look to the Parent
        $parent_index = $this->getParentIndex($normalized_key);
        
        // Mutator for parent's Values (i.e. children) -- akin to a custom type mutator?
        if (isset($this->meta[$parent_index]['values']['mutator'])) {
            if (method_exists($this,
                $this->meta[$parent_index]['values']['mutator'])) {
                return $this->meta[$parent_index]['values']['mutator'];
            }
            throw new InvalidMutatorException('Mutator method "' . $this->meta[$parent_index]['values']['mutator'] . '"does not exist.  Defined at index "' . $parent_index . '" values mutator');
        }
        // Parent Type-level
        if (isset($this->meta[$parent_index]['values']['type'])) {
            $functionName = $this->getFunctionName('mutateType',
                $this->meta[$parent_index]['values']['type']);
            if (!method_exists($this, $functionName)) {
                throw new InvalidMutatorException('Mutator method "' . $functionName . '"does not exist. Type defined for values meta at index "' . $parent_index . '"');
            }

            return $functionName;
        }
        
        return 'mutateTypeUnknown';
    }
    
    /**
     * Return a valid function name, potentially assembled from location parts.
     *
     * @param $prefix string
     * @param $descriptor string - from the meta index location
     *
     * @return bool|string
     */
    protected function getFunctionName($prefix, $descriptor)
    {
        if (!$prefix || !$descriptor) {
            return false;
        }
        $descriptor = trim($descriptor, '.');
        $parts = explode('.', $descriptor);
        $parts = array_map('strtolower', $parts);
        $parts = array_map('ucfirst', $parts);
        
        return $prefix . implode('', $parts);
    }
    
    /**
     * Find the parent index from the given index, e.g. find ".foo" from ".foo.bar".
     *
     * @param $normalized_key
     *
     * @return string
     */
    protected function getParentIndex($normalized_key)
    {
        $result = substr($normalized_key, 0, strrpos($normalized_key, '.'));
        
        return ($result) ? $result : '.';
    }
    
    /**
     * Returns the function name used to mutate composite values (e.g. arrays, hashes) being set at the given $index.
     * The composite mutators will loop over the composite values and will in turn call value-mutators on individual
     * scalar values.
     * Type-mutator-methods are named using a prefix of "mutateType"; field-mutators use the prefix of "set".
     *
     * @param $index string (non-normalized)
     *
     * @return string function name
     *
     * @throws InvalidMutatorException
     */
    protected function getCompositeMutator($index)
    {
        $normalized_key = $this->getNormalizedKey($index);

        $meta = $this->getMeta($index);
        // Field-level Mutator
        if ($normalized_key != '.') {
            if (isset($meta['mutator'])) {
                $functionName = $meta['mutator'];
                if (method_exists($this, $functionName)) {
                    return $functionName;
                }
                throw new InvalidMutatorException('Mutator method "' . $meta['mutator'] . '"does not exist.  Defined at index "' . $normalized_key . '"');
            }
        }
        // Type-level Mutator
        if (isset($this->meta[$normalized_key]['type'])) {
            $functionName = $this->getFunctionName('mutateType',
                $this->meta[$normalized_key]['type']);
            if (!method_exists($this, $functionName)) {
                throw new InvalidMutatorException('Mutator method "' . $functionName . '"does not exist. Type defined in meta at index "' . $normalized_key . '"');
            }
            
            return $functionName;
        }
        // Are we setting items in an array?
        if (is_numeric(trim($normalized_key,'.'))) {
            $functionName = $this->getFunctionName('mutateType', $meta['type']);
            if (!method_exists($this, $functionName)) {
                throw new InvalidMutatorException('Mutator method "' . $functionName . '"does not exist. Type defined in meta at index "' . $normalized_key . '"');
            }

            return $functionName;
        }

        // Fallback
        return 'mutateTypeHash';
    }
    
    /**
     * This determines what types of data is valid in our internal DTO storage.
     *
     * @param $value
     *
     * @return mixed
     *
     * @throws InvalidDataTypeException
     */
    protected function isValidValue($value)
    {
        if (is_null($value) || is_scalar($value) || is_array($value) || $value instanceof self || $value instanceof \stdClass) {
            return true;
        }
        
        return false;
    }
    
    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this[$name];
    }


    /**
     * Accessed when the object is written to via object notation.
     *
     * @param $name
     * @param $value
     *
     * @throws InvalidLocationException
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }
    
    /**
     * Alternative setter: Expose the $force flag.
     * This can only be used to set values of the immediate children. Dot-notation to reference deeper data is NOT supported.
     *
     * @param $index mixed
     * @param $value mixed
     * @param bool $bypass filters
     */
    public function set($index, $value, $bypass = false)
    {
        $this->offsetSet($index, $value, $bypass);
    }
    
    /**
     * TODO: make final?
     *
     * @param mixed $index
     * @param mixed $newval
     * @param bool $bypass filters if true
     *
     * @throws AppendException
     * @throws InvalidLocationException
     */
    public function offsetSet($index, $newval, $bypass = false)
    {
        if ($index === null && !$this->isAppendable($index)) {
            throw new AppendException('Append operations at location "' . $this->getNormalizedKey($index) . '" are not allowed. Set type to "array".');
        }
        // Beware string equivalence with integers: 0 == '.' so we must use ===
        if ($index === '.') {
            $newval = ($bypass) ? $newval : $this->filterRoot($newval);
            parent::__construct($newval); // store value as is
            return;
        }
        
        if ($bypass) {
            parent::offsetSet($index, $newval); // store the value on the ArrayObject
            return;
        }
        
        // Allowed to set specific indexes in an array?
        $meta = $this->getMeta($this->getParentIndex($index));
        if ($meta['type'] == 'array' && is_numeric($index) && !parent::offsetExists($index)) {
            throw new InvalidLocationException('Location does not exist in array '.$index);
        }
        
        try {
            $newval = $this->filterNode($newval, $index);
            parent::offsetSet($index, $newval); // store the value on the ArrayObject
        } catch (\Exception $e) {
            $this->handleException($e, $index, $newval); // Do not store the value
        }
    }
    
    /**
     * Determine if the given location can be appended to.
     *
     * @param $index
     *
     * @return bool
     */
    protected function isAppendable($index)
    {
        // TODO: look for meta flag? appendable?
        return in_array($this->getMeta($index)['type'], ['array']);
    }
    
    /**
     * @param $name string attribute name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this[$name]);
    }
    
    /**
     * This helps remind the user that they tried to access the ArrayObject in the wrong context.
     *
     * @return string
     */
    public function __toString()
    {
        return 'Array';
    }
    
    /**
     * Append a value to the end of an array.  Defers to offsetSet to determine if location is valid for appending.
     * See http://php.net/manual/en/arrayobject.append.php.
     *
     * @param mixed $val
     */
    public function append($val)
    {
        $this->offsetSet(null, $val);
    }
    
    /**
     * Dot-notation getter: alternative to array and/or object get syntax.
     * See http://php.net/manual/en/language.references.return.php.
     *
     * @param $dotted_key
     *
     * @return mixed
     */
    public function get($dotted_key)
    {
        $parts = explode('.', trim($dotted_key, '.'));
        
        $location = $this->{array_shift($parts)}; // prime the pump with the first location
        
        foreach ($parts as $k) {
            $location = $location->{$k};
        }
        
        return $location;
    }
    
    /**
     * TODO: make final?
     *
     * @param mixed $index
     *
     * @return mixed
     *
     * @throws InvalidLocationException
     */
    public function offsetGet($index)
    {
        //  Remember: isset() returns false if the value is null
        if ($this->isValidTargetLocation($index, $this->template)) {
            // This bit allows us to dynamically deepen the object structure
            // TODO: does this need to return a child DTO vs. a "sub-DTO"?
            if (!array_key_exists($index, $this)) {
                $classname = get_called_class();
                $child = new $classname([],
                    $this->getTemplateSubset($index, $this->template),
                    $this->getMetaSubset($index, $this->meta));
                $this->offsetSet($index, $child);
            }
            
            return parent::offsetGet($index);
        } else {
            throw new InvalidLocationException('Index not defined in template: ' . $index);
        }
    }
    
    /**
     * Convert the specified arrayObj to a stdClass object.  Ultimately, this is a decorator around the toJson() method.
     *
     * @param Dto $arrayObj
     *
     * @return object stdClass
     */
    public function toObject(Dto $arrayObj = null)
    {
        return json_decode($this->toJson(false, $arrayObj));
    }
    
    /**
     * Convert the specified arrayObj to JSON.  Ultimately, this is a decorator around the toArray() method.
     * TODO: consider overriding the serialize() method
     * @param bool $pretty
     * @param Dto $arrayObj
     *
     * @return string
     */
    public function toJson($pretty = false, Dto $arrayObj = null)
    {
        return ($pretty) ? json_encode($this->toArray($arrayObj),
            JSON_PRETTY_PRINT) : json_encode($this->toArray($arrayObj));
    }
    
    /**
     * The input allows the injection of a foreign Dto $arrayObj for recursive resolving of children DTOs.
     *
     * @param Dto|null $arrayObj
     *
     * @return array
     */
    public function toArray(Dto $arrayObj = null)
    {
        $arrayObj = ($arrayObj) ? $arrayObj : $this;
        $output = [];
        foreach ($arrayObj as $k => $v) {
            if ($v instanceof self) {
                $output[$k] = $this->toArray($v);
            } else {
                $output[$k] = $v;
            }
        }
        
        return $output;
    }
    
    /**
     * Called internally by the filterNode() method.
     *
     * @param $value mixed
     * @param $index string
     * @param $meta array
     *
     * @return mixed
     *
     * @throws InvalidDataTypeException
     */
    protected function mutateTypeArray($value, $index, array $meta = [])
    {
        $value = (is_array($value)) ? array_values($value) : $value;
        
        return $this->mutateTypeHash($value, $index, $meta);
    }
    
    /**
     * Called internally by the filterNode() method.  This is the powerhouse mapping function: it returns a "sub-Dto",
     * with a subset of the template and meta definitions.
     *
     * @param $value mixed
     * @param $index string
     * @param $meta array
     *
     * @return mixed
     *
     * @throws InvalidDataTypeException
     */
    protected function mutateTypeHash($value, $index, array $meta = [])
    {
        // Is the "sub-Dto" an instance of the parent DTO? Or does it use a specific sub-class?
        if (isset($this->meta[$index]['type']['class']) && $this->meta[$index]['type'] == 'dto') {
            $classname = $this->meta[$index]['type']['class'];
            return new $classname((array)$value);
        }
        elseif (isset($this->meta['.']['anonymous']) && $this->meta['.']['anonymous']
            && isset($this->meta['.']['values']['type'])
            && $this->meta['.']['values']['type'] == 'dto'
            && isset($this->meta['.']['values']['class'])) {
            $classname = $this->meta['.']['values']['class'];
            return new $classname((array)$value);
        }
        else {
            // Return a subset of the parent DTO
            $classname = get_called_class();
            return new $classname((array)$value,
                $this->getTemplateSubset($index, $this->template),
                $this->getMetaSubset($index, $this->meta));
        }
    }
    
    /**
     * @param $index
     *
     * @return bool
     */
    protected function isNullable($index)
    {
        $meta = $this->getMeta($index);
        
        return (bool)(isset($meta['nullable']) && $meta['nullable']);
    }
    
    /**
     * @param $index
     * @param array $template
     *
     * @return array|mixed
     */
    protected function getTemplateSubset($index, array $template)
    {
        $index = ltrim($index, '.'); // denormalize
        return (isset($template[$index]) && is_array($template[$index])) ? $template[$index] : [];
    }
    
    /**
     * Returns the part of the supplied $meta array whose keys begin with the $prefix and re-index the array with the
     * prefix removed. E.g. if a prefix of "foo" (or ".foo") is supplied, any meta keys beginning with ".foo" will be
     * returned and re-indexed to remove the prefix, e.g. ".foo.bar" --> ".bar", or ".foo" --> "."
     * This is used to get a subset of the meta data when instantiating a child class.
     *
     * @param $prefix string
     * @param $meta array
     *
     * @return array
     */
    protected function getMetaSubset($prefix, array $meta)
    {
        $trimmed = [];
        $prefix = $this->getNormalizedKey($prefix);
        
        foreach ($meta as $dotted_key => $value) {
            if (substr($dotted_key, 0, strlen($prefix)) == $prefix) {
                // shift something like ".foo.bar" to ".bar"
                if ($new_key = substr($dotted_key, strlen($prefix))) {
                    $trimmed[$new_key] = $value;
                } // shift something like ".foo" to "."
                elseif ($prefix != '.') {
                    $trimmed['.'] = $value;
                }
            }
        }
        
        return $trimmed;
    }
    
    /**
     * Called internally by the filterNode() method.
     *
     * @param $value mixed
     * @param $index string
     * @param $meta array
     *
     * @return bool
     */
    protected function mutateTypeBoolean($value, $index, array $meta = [])
    {
        return boolval($value);
    }
    
    /**
     * Called internally by the filterNode() method.
     *
     * @param $value mixed
     * @param $index string
     *
     * @return int
     *
     * @throws InvalidDataTypeException
     */
    protected function mutateTypeDto($value, $index, array $meta = [])
    {
        // Handle appending values
        if ((!is_null($index) && !isset($meta['class'])) || (is_null($index) && !isset($meta['values']['class']))) {
            throw new \InvalidArgumentException('Meta information for DTO at index "' . $this->getNormalizedKey($index) . '" requires "class" parameter in ' . get_called_class());
        }
        
        $classname = (is_null($index)) ? $meta['values']['class'] : $meta['class'];
        
        if (is_null($value)) {
            return new $classname();
        }
        
        if ($value instanceof $classname) {
            return $value;
        }

        // This solves problems of injecting a deeply nested array into the constructor: we convert the array into a DTO
        if (is_array($value) && $meta['type'] === 'dto') {
            return new $classname($value);
        }

        // TODO: other data types?  array? Hash?
        throw new InvalidDataTypeException($index . ' value must be instance of ' . $classname);
    }
    
    /**
     * Called internally by the filterNode() method.
     *
     * @param $value mixed
     * @param $index string
     * @param $meta array
     *
     * @return float
     */
    protected function mutateTypeFloat($value, $index, array $meta = [])
    {
        return floatval($value);
    }
    
    /**
     * Called internally by the filterNode() method.
     *
     * @param $value mixed
     * @param $index string
     * @param $meta array
     * @return int
     */
    protected function mutateTypeInteger($value, $index, array $meta = [])
    {
        return intval($value);
    }
    
    /**
     * Alias function: we have better things to do than argue about whether strings are scalars.
     *
     * @param $value mixed
     * @param $index string
     * @param $meta array
     *
     * @return string
     */
    protected function mutateTypeString($value, $index, array $meta = [])
    {
        return $this->mutateTypeScalar($value, $index, $meta);
    }
    
    /**
     * Called internally by the filterNode() method.
     *
     * @param $value mixed
     * @param $index string
     * @param $meta array
     *
     * @return string
     */
    protected function mutateTypeScalar($value, $index, array $meta = [])
    {
        return strval($value);
    }
    
    /**
     * Called internally by the filterNode() method.
     * Type "unknown" is used in cases where:
     *  1. a non-populated index is declared as an array or hash, but without use of the "values" qualifier. (aka ambiguous hash)
     *  2. a template contains nested hashes but it does not have explicit meta data (i.e. the inferred meta data does
     *      not include the "values" quantifier.
     *
     * @param $value mixed
     *
     * @return mixed
     *
     * @throws InvalidDataTypeException
     */
    protected function mutateTypeUnknown($value)
    {
        return $value; // do nothing
    }
}
