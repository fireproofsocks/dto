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
     * @param array $input    values filtered against the $template and $meta
     * @param array $template template (i.e. default values) with loosely typed values
     * @param array $meta     extra info about the template data.
     */
    public function __construct(array $input = [], array $template = [], array $meta = [])
    {
        echo "vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv\n";
        $this->setFlags(0);
        $arg_list = func_get_args();

        // We need to be able to override the class variables when the input variables are empty.
        $this->template = (isset($arg_list[1])) ? $arg_list[1] : $this->template;
        $this->meta = (isset($arg_list[2])) ? $arg_list[2] : $this->meta;

        $this->meta = $this->normalizeMeta($this->meta);
        $this->meta = $this->autoDetectTypes($this->template, $this->meta);

        $input = ($input) ? $input : $this->template; // You cannot override $this->template with an empty input

        print_r($input);
        print_r($this->meta);
        print_r($this->template);
        echo "^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n\n";

        parent::__construct($this->filterRoot($input)); // store the value in the ArrayObject
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
        // print '['.__LINE__ .'] '.__FUNCTION__ ."\n";
        $normalized = [];
        foreach ($meta as $key => $value) {
            $key = $this->getNormalizedKey($key);
            if (!$this->isValidMetaKey($key)) {
                throw new InvalidMetaKeyException('The key "'.$key.'" contains invalid characters or points to an invalid location."');
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
        // print '['.__LINE__ .'] '.__FUNCTION__ .' key: '. $key . ' --> '.'.' . trim($key, '.') ."\n";
        return '.'.trim($key, '.');
    }

    /**
     * @param $key
     *
     * @return bool
     */
    protected function isValidMetaKey($key)
    {
        // print '['.__LINE__ .'] '.__FUNCTION__ ."\n";
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
     * @param array $meta     (normalized)
     *
     * @return array
     *
     * @throws InvalidDataTypeException
     */
    protected function autoDetectTypes($template, $meta)
    {
        // print '['.__LINE__ .'] '.__FUNCTION__ ."\n";
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
        echo '['.__LINE__.'] '.__FUNCTION__.' type: '.$type."\n";
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
        // print '['.__LINE__ .'] '.__FUNCTION__ ."\n";
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
        // If the $meta[.][type][values] expects an array (non scalar), then we should bail here if the data type doesn't match
        $meta = $this->getMeta('.');
        
        foreach ($value as $k => $v) {
            // isValidLocation ?
//            if ($this->isScalarType($meta['values']['type'])) {
//                if (!is_scalar($v) && !is_null($v)) {
//                    throw new InvalidDataTypeException('Cannot write non-scalar value to scalar locations at root');
//                }
//
//            } // Composite Types
//            else {
//                if (is_scalar($v) && !is_null($v)) {
//                    throw new InvalidDataTypeException('Cannot write scalar values to non-scalars location at root');
//                }
//
//            }
            $child_index = '.'.$k;
            try {
                $value[$k] = $this->filterNode($v, $child_index);
            } catch (\Exception $e) {
                unset($value[$k]);
            }
        }

        return $value;
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
        echo '['.__LINE__.'] '.__FUNCTION__.' index: "'.$index.'"'."\n";
        if (empty($template) || is_null($index)) {
            echo '['.__LINE__.']     ----> empty'."\n";
            return true;
        }
        
        $meta = $this->getMeta($index);
        if (isset($meta['ambiguous'])) {
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
     * @param array $template
     * @return bool
     *
     * @throws InvalidDataTypeException
     */
    public function isValidMapping($value, $index, array $template) {
    
        
        
        // Check for compatible value/target
        $normalized_key = $this->getNormalizedKey($index);
        $meta = $this->getMeta($index);
        echo '['.__LINE__.'] '.__FUNCTION__.' index: "'.$normalized_key.'"'."\n";
        print_r($value); print "\n";
        print_r($template); print "\n";
        print_r($meta); print "\n";
        
        
        // Append operation
        if (is_null($index)) {
            $target_type = $meta['values']['type'];
        }
        else {
            $target_type = $meta['type'];
        }
        
        if ($target_type == 'unknown') {
            return true;
        } // Scalar Types
        elseif ($this->isScalarType($target_type)) {
            if (is_scalar($value) || is_null($value)) {
                return true;
            }
            throw new InvalidDataTypeException('Cannot write non-scalar value to scalar location "'.$normalized_key.'"');
        } // Composite Types
        else {
            if (!is_scalar($value) || is_null($value)) {
                return true;
            }
            throw new InvalidDataTypeException('Cannot write scalar value to non-scalar location "'.$normalized_key.'"');
        }
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
        //print '['.__LINE__ .'] '.__FUNCTION__ ."\n";
        $normalized_key = $this->getNormalizedKey($index);

        if (!isset($this->meta[$normalized_key])) {
            return ['type' => 'unknown']; // TODO: throw exception?
        }

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
        echo '['.__LINE__.'] '.__FUNCTION__."\n";
        
        // Test for root-level arrays and index is null?
        //print '???'; exit;
        if ($index == '.') {
            throw new InvalidLocationException('filterRoot must be used for root key (.)');
        }
        
        $normalized_key = $this->getNormalizedKey($index);
        
        if (!$this->isValidTargetLocation($index, $this->template)) {
            throw new InvalidLocationException('Index "'.$normalized_key.'" not valid for writing');
        }
    
        if (!$this->isValidMapping($value, $index, $this->template)) {
            throw new InvalidLocationException('Invalid mapping at "'.$normalized_key.'"');
        }
        
        
        //$mutatorFunction = $this->getMutator($value, $normalized_key);
        $mutatorFunction = $this->getMutator($value, $index);
        
        echo '['.__LINE__.'] ----> applying mutator '.$mutatorFunction. ' at index "'.$index.'" (normalized: '.$normalized_key.')'."\n";
        // Final gatekeeping
        $value = $this->{$mutatorFunction}($value, $index);

        if (!$this->isValidValue($value)) {
            throw new InvalidDataTypeException('Invalid data type cannot be written at location "'.$normalized_key.'"');
        }

        return $value;
    }

    /**
     * Logic for resolving which mutator function to use.  It can be a bit tricky when data is not explicitly defined.
     * This does not try to determine the validity of mutating a scalar value onto a composite location or a composite
     * value onto a scalar location (SRP).  See isValidTargetLocation for that.
     *
     * @param $value mixed
     * @param $index string
     *
     * @return string
     */
    protected function getMutator($value, $index)
    {
        echo '['.__LINE__.'] '.__FUNCTION__.' index: '.$index."\n";
        if ($index == null) {
            return $this->getValueMutator($index);
        }

        $meta = $this->getMeta($index);

        // Unknown can be either/or -- it changes depending on the value type
        if ($meta['type'] == 'unknown') {
            return (is_scalar($value) || is_null($value)) ? $this->getValueMutator($index) : $this->getCompositeMutator($index);
        }

        //print __FUNCTION__ . ':' . __LINE__ . ' meta: '.print_r($meta,true).")\n";
        return ($this->isScalarType($meta['type'])) ? $this->getValueMutator($index) : $this->getCompositeMutator($index);
    }

    /**
     * Returns the function name used to mutate scalar values being set at the given $index.  This looks for definitions
     * in different places than the getCompositeMutatorFunctionName() method.
     * Type-mutator-methods are named using a prefix of "mutateType"; field-mutators use the prefix of "set".
     *
     * @param $index (non-normalized)
     *
     * @return string function name
     *
     * @throws InvalidMutatorException
     */
    protected function getValueMutator($index)
    {
        echo '['.__LINE__.'] '.__FUNCTION__."\n";
        $normalized_key = $this->getNormalizedKey($index);
        // Field-level mutator
        if ($normalized_key != '.') {
            $functionName = $this->getFunctionName('mutate', $index);
            if (method_exists($this, $functionName)) {
                return $functionName;
            }
        }
        // Type-level Mutator
        if (isset($this->meta[$normalized_key]['type']) && $this->isScalarType($this->meta[$normalized_key]['type'])) {
            $functionName = $this->getFunctionName('mutateType', $this->meta[$normalized_key]['type']);
            if (!method_exists($this, $functionName)) {
                throw new InvalidMutatorException('Mutator method "'.$functionName.'"does not exist. Type defined in meta at index "'.$normalized_key.'"');
            }

            return $functionName;
        }
        // Value-Level Mutator (look to the parent)
        $parent_index = $this->getParentIndex($normalized_key);
        if (isset($this->meta[$parent_index]['values']['type'])) {
            $functionName = $this->getFunctionName('mutateType', $this->meta[$parent_index]['values']['type']);
            if (!method_exists($this, $functionName)) {
                throw new InvalidMutatorException('Mutator method "'.$functionName.'"does not exist. Type defined for values meta at index "'.$parent_index.'"');
            }

            return $functionName;
        }

        return 'mutateTypeUnknown';
    }

    /**
     * Return a valid function name.
     *
     * @param $prefix string
     * @param $descriptor string - from the meta index location
     *
     * @return bool|string
     */
    protected function getFunctionName($prefix, $descriptor)
    {
        // print '['.__LINE__ .'] '.__FUNCTION__ ."\n";
        if (!$prefix || !$descriptor) {
            return false;
        }
        $descriptor = trim($descriptor, '.');
        $parts = explode('.', $descriptor);
        $parts = array_map('strtolower', $parts);
        $parts = array_map('ucfirst', $parts);

        return $prefix.implode('', $parts);
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
        echo '['.__LINE__.'] '.__FUNCTION__."\n";
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
        echo '['.__LINE__.'] '.__FUNCTION__.' index: '.$index."\n";
        $normalized_key = $this->getNormalizedKey($index);
        // Field-level Mutator
        if ($normalized_key != '.') {
            $functionName = $this->getFunctionName('mutate', $index);
            if (method_exists($this, $functionName)) {
                return $functionName;
            }
        }
        // Type-level Mutator
        if (isset($this->meta[$normalized_key]['type'])) {
            $functionName = $this->getFunctionName('mutateType', $this->meta[$normalized_key]['type']);
            if (!method_exists($this, $functionName)) {
                throw new InvalidMutatorException('Mutator method "'.$functionName.'"does not exist. Type defined in meta at index "'.$normalized_key.'"');
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
        echo '['.__LINE__.'] '.__FUNCTION__."\n";
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
        echo '['.__LINE__.'] '.__FUNCTION__.' name: '.$name."\n";

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
        echo '['.__LINE__.'] '.__FUNCTION__.' name: '.$name."\n";

        return $this->set($name, $value);
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
        echo '['.__LINE__.'] '.__FUNCTION__."\n";
        $this->offsetSet($index, $value, $bypass);
        // Special case for the root node: a dot means we're talking about THIS ArrayObject

//        if ($index == '.') {
//            if ($bypass) {
//                parent::__construct($value); // store value as is
//            } else {
//                $this->__construct($value, $this->template, $this->meta); // re-run constructor will re-run filters
//            }
//        } else {
//            $this->offsetSet($index, $value, $bypass);
//        }
    }

    /**
     * TODO: make final?
     *
     * @param mixed $index
     * @param mixed $newval
     * @param bool  $bypass filters if true
     *
     * @throws AppendException
     */
    public function offsetSet($index, $newval, $bypass = false)
    {
        echo '['.__LINE__.'] '.__FUNCTION__."\n";
        if ($index == null && !$this->isAppendable($index)) {
            throw new AppendException('Append operations at location "'.$this->getNormalizedKey($index).'" are not allowed. Set type to "array".');
        }
        if ($index == '.') {
            $newval = ($bypass) ? $newval : $this->filterRoot($newval);
            parent::__construct($newval); // store value as is
        } else {
            // TODO: try/catch?
            $newval = ($bypass) ? $newval : $this->filterNode($newval, $index);
            parent::offsetSet($index, $newval); // store the value on the ArrayObject
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
        echo '['.__LINE__.'] '.__FUNCTION__."\n";
        // TODO: look for meta flag? appendable?
        return in_array($this->getMeta($index)['type'], ['array']);
    }

    /**
     * @param $name attribute name
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
        echo '['.__LINE__.'] '.__FUNCTION__."\n";
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
        echo '['.__LINE__.'] '.__FUNCTION__.' dotted key: '.$dotted_key."\n";
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
        echo '['.__LINE__.'] '.__FUNCTION__.' index: '.$index."\n";
        //  Remember: isset() returns false if the value is null
        if (empty($this->template) || array_key_exists($index, $this->template)) {
            // This bit allows us to dynamically deepen the object structure
            //if (!isset($this[$index])) {
            if (!array_key_exists($index, $this)) {
                $classname = get_called_class();
                echo '['.__LINE__.']  (new '.$classname.'!) '."\n";
                $child = new $classname([], $this->getTemplateSubset($index, $this->template),
                    $this->getMetaSubset($index, $this->meta));
                $this->offsetSet($index, $child);
                //$this->offsetSet($index, new $classname()); // dynamically deepen the object structure just in time
            }

            return parent::offsetGet($index);
        } else {
            throw new InvalidLocationException('Index not defined in template: '.$index);
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
        echo '['.__LINE__.'] '.__FUNCTION__."\n";

        return json_decode($this->toJson(false, $arrayObj));
    }

    /**
     * Convert the specified arrayObj to JSON.  Ultimately, this is a decorator around the toArray() method.
     *
     * @param bool $pretty
     * @param Dto  $arrayObj
     *
     * @return string
     */
    public function toJson($pretty = false, Dto $arrayObj = null)
    {
        echo '['.__LINE__.'] '.__FUNCTION__."\n";

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
        echo '['.__LINE__.'] '.__FUNCTION__."\n";
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
     * @param $value
     * @param $index
     *
     * @return mixed
     *
     * @throws InvalidDataTypeException
     */
    protected function mutateTypeArray($value, $index)
    {
        echo '['.__LINE__.'] '.__FUNCTION__.' index: '.$index."\n";
        $value = (is_array($value)) ? array_values($value) : $value;

        return $this->mutateTypeHash($value, $index);
    }

    /**
     * Called internally by the filterNode() method.  This is the powerhouse mapping function.
     *
     * @param $value mixed
     * @param $index string
     *
     * @return mixed
     *
     * @throws InvalidDataTypeException
     */
    protected function mutateTypeHash($value, $index)
    {
        echo '['.__LINE__.'] '.__FUNCTION__.' index: '.$index."\n";

        $classname = get_called_class();

        if (is_null($value)) {
            echo '['.__LINE__.']  (new '.$classname.'!) '."\n";

            return ($this->isNullable($index)) ? null : new $classname([],
                $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
        }

        echo '['.__LINE__.']  (new '.$classname.'!) '."\n";
        //print_r($value); print_r($this->getTemplateSubset($index, $this->template)); print_r($this->getMetaSubset($index, $this->meta)); exit;
        $child = new $classname((array) $value, $this->getTemplateSubset($index, $this->template),
            $this->getMetaSubset($index, $this->meta));

        return $child;
    }

    /**
     * @param $index
     *
     * @return bool
     */
    protected function isNullable($index)
    {
        echo '['.__LINE__.'] '.__FUNCTION__."\n";
        $meta = $this->getMeta($index);

        return (bool) (isset($meta['nullable']) && $meta['nullable']);
    }

    /**
     * @param $index
     * @param array $template
     *
     * @return array|mixed
     */
    protected function getTemplateSubset($index, array $template)
    {
        // print '['.__LINE__ .'] '.__FUNCTION__ ."\n";
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
        // print '['.__LINE__ .'] '.__FUNCTION__ ."\n";
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
     *
     * @return bool
     */
    protected function mutateTypeBoolean($value, $index)
    {
        echo '['.__LINE__.'] '.__FUNCTION__.' index: '.$index."\n";

        return (is_null($value) && $this->isNullable($index)) ? null : boolval($value);
    }

    /**
     * Called internally by the filterNode() method.
     * TODO: test.
     *
     * @param $value mixed
     * @param $index string
     *
     * @return int
     *
     * @throws InvalidDataTypeException
     */
    protected function mutateTypeDto($value, $index)
    {
        echo '['.__LINE__.'] '.__FUNCTION__.' index: '.$index."\n";

        $meta = $this->getMeta($index);

        if (!isset($meta['class'])) {
            throw new \InvalidArgumentException('Meta information for DTO at index "'.$this->getNormalizedKey($index).'" requires "class" parameter.');
        }

        $classname = $meta['class'];

        if (is_null($value)) {
            return ($this->isNullable($index)) ? null : new $classname();
        }

        if ($value instanceof $classname) {
            return $value;
        }

        // TODO: other data types?  array? Hash?
        //print_r($value); exit;
        throw new InvalidDataTypeException($index.' value must be instance of '.$classname);
    }

    /**
     * Called internally by the filterNode() method.
     *
     * @param $value mixed
     * @param $index string
     *
     * @return float
     */
    protected function mutateTypeFloat($value, $index)
    {
        echo '['.__LINE__.'] '.__FUNCTION__.' index: '.$index."\n";

        return (is_null($value) && $this->isNullable($index)) ? null : floatval($value);
    }

    /**
     * Called internally by the filterNode() method.
     *
     * @param $value mixed
     * @param $index string
     *
     * @return int
     */
    protected function mutateTypeInteger($value, $index)
    {
        echo '['.__LINE__.'] '.__FUNCTION__.' index: '.$index."\n";

        return (is_null($value) && $this->isNullable($index)) ? null : intval($value);
    }

    /**
     * Convenience function (because really, we have better things to do than argue about whether strings are scalars).
     *
     * @param $value mixed
     * @param $index string
     *
     * @return string
     */
    protected function mutateTypeString($value, $index)
    {
        return $this->mutateTypeScalar($value, $index);
    }

    /**
     * Called internally by the filterNode() method.
     *
     * @param $value mixed
     * @param $index string
     *
     * @return string
     */
    protected function mutateTypeScalar($value, $index)
    {
        echo '['.__LINE__.'] '.__FUNCTION__.' index: '.$index."\n";

        return (is_null($value) && $this->isNullable($index)) ? null : strval($value);
    }

    /**
     * Called internally by the filterNode() method.
     * Type "unknown" is used in cases where:
     *  1. a non-populated index is declared as an array or hash, but without use of the "values" qualifier. (aka ambiguous hash)
     *  2. a template contains nested hashes but it does not have explicit meta data (i.e. the inferred meta data does
     *      not include the "values" quantifier.
     *
     * @param $value mixed
     * @param $index string
     *
     * @return mixed
     *
     * @throws InvalidDataTypeException
     */
    protected function mutateTypeUnknown($value, $index)
    {
        echo '['.__LINE__.'] '.__FUNCTION__.' index: '.$index."\n";

        return $value; // do nothing
    }
}
