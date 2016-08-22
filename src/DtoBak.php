<?php
namespace Dto;

use Dto\Exceptions\InvalidDataTypeException;
use Dto\Exceptions\InvalidLocationException;
use Dto\Exceptions\InvalidMetaKeyException;

/**
 * Class Dto (Data Transfer Object)
 *
 * Allows object schemas to be defined and helps to normalize object access.
 *
 * See http://php.net/manual/en/class.arrayobject.php
 * Some ideas from https://symfony.com/doc/current/components/property_access/introduction.html#installation
 * And others from http://json-schema.org/
 */
class Dto extends \ArrayObject
{
    
    protected $template = [];
    protected $meta = [];
    
    /**
     * Dto constructor.
     *
     * @param array $input starter data, filtered against the $template and $meta (if supplied)
     * @param array $template generic data template (i.e. default values) with loosely typed values
     * @param array $meta definitions
     */
    public function __construct(array $input = [], array $template = [], array $meta = [])
    {
        $this->setFlags(0);
        $arg_list = func_get_args();
        
        // We need to be able to override the class variables when the input variables are empty.
        $this->template = (isset($arg_list[1])) ? $arg_list[1] : $this->template;
        $this->meta = (isset($arg_list[2])) ? $arg_list[2] : $this->meta;
        
        $this->meta = $this->normalizeMeta($this->meta);
        $this->meta = $this->autoDetectTypes($this->template, $this->meta);
        //print_r($input); exit;
        $this->setRootNode($input);
    }
    
    public function x($input)
    {
        parent::__construct($input);
    }
    /**
     * Append a value to the end of an array.  Defers to offsetSet to determine if location is valid for appending.
     * See http://php.net/manual/en/arrayobject.append.php
     * @param mixed $val
     */
    public function append($val)
    {
        return $this->offsetSet(null, $val);
    }
    
    /**
     * Fill in any empty spots in the $this->meta definitions based on what is provided in the $template.
     * This function only detect the first "left-most" (i.e. top-level) nodes: deeper structures will be iteratively
     * passed to child instances.  For instance, $template = ['is_on' => false] would have the effect as explicitly
     * defining the corresponding $meta key as 'boolean': $meta = ['is_on' => ['type => 'boolean']];
     *
     * Modifies class-level $this->meta.
     *
     * @param array $template
     * @param array $meta (normalized)
     * @return array
     * @throws InvalidDataTypeException
     */
    protected function autoDetectTypes($template, $meta)
    {
        foreach ($template as $index => $v) {
            $meta_key = $this->getNormalizedKey($index);
            $meta[$meta_key]['nullable'] = (isset($meta[$meta_key]['nullable'])) ? $meta[$meta_key]['nullable'] : false;
            
            if (isset($meta[$meta_key]['type'])) {
                continue; // skip detection if type is already set
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
            elseif ($this->isHash($template[$index])) {
                $meta[$meta_key]['type'] = 'hash';
            } // Arrays
            elseif (is_array($template[$index])) {
                $meta[$meta_key]['type'] = 'array';
            }
            
        }
        
        return $meta;
    }
    
    /**
     * Gets the official "normalized" fully-qualified version of the dotted-key, which begin with a leading dot
     *
     * @param $key
     * @return bool|string
     */
    protected function getNormalizedKey($key)
    {
        return '.' . trim($key, '.');
    }
    
    /**
     * @param $key
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
     * Returns the part of the supplied $meta array whose keys begin with the $prefix and re-index the array with the
     * prefix removed. E.g. if a prefix of "foo" (or ".foo") is supplied, any meta keys beginning with ".foo" will be
     * returned and re-indexed to remove the prefix, e.g. ".foo.bar" --> ".bar", or ".foo" --> "."
     * This is used to get a subset of the meta data when instantiating a child class.
     *
     * @param $prefix string
     * @param $meta array
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
        //print '???'; print_r($trimmed); exit;
        //if (isset($meta['.']['values']) && !isset($trimmed['.'])) {
//        if (isset($meta['.']['values'])) {
//            $trimmed['.'] = $meta['.']['values'];
//        }
        if (isset($trimmed['.']['values'])) {
            $trimmed['.'] = $trimmed['.']['values'];
        }
        
        return $trimmed;
    }
    
    
    /**
     * @param $index
     * @param array $template
     * @return array|mixed
     */
    protected function getTemplateSubset($index, array $template)
    {
        return (isset($template[$index]) && is_array($template[$index])) ? $template[$index] : [];
    }
    
    /**
     * Normalize the keys used to define meta data.  For dev's UX, we allow keys like "x", but internally, we consider all
     * locations defined in the meta definition to begin with "." -- so all keys are normalized to the leading dot format.
     * @param $meta
     * @return array
     * @throws InvalidMetaKeyException
     */
    protected function normalizeMeta($meta)
    {
        $normalized = [];
        foreach ($meta as $key => $value) {
            $key = $this->getNormalizedKey($key);
            if (!$this->isValidMetaKey($key)) {
                throw new InvalidMetaKeyException('The key "' . $key . '" contains invalid characters or points to an invalid location."');
            }
            
            $normalized[$key] = $value;
        }
        if (!isset($normalized['.']['type'])) {
            $normalized['.']['type'] = 'hash';
        }
        if (!isset($normalized['.']['values'])) {
            $normalized['.']['values'] = ['type' => 'unknown'];
        }
        return $normalized;
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
     * Accessed when the object is written to via object notation.
     *
     * @param $name
     * @param $value
     * @throws InvalidLocationException
     */
    public function __set($name, $value)
    {
        return $this->offsetSet($name, $value);
    }
    
    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this[$name];
    }
    
    
    /**
     * Dot-notation getter: alternative to array and/or object get syntax.
     * See http://php.net/manual/en/language.references.return.php
     *
     * @param $dotted_key
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
     * Alternative setter: Expose the $force flag.
     * This can only be used to set values of the immediate children. Dot-notation to reference deeper data is NOT supported.
     *
     * @param $index
     * @param $value
     * @param bool $force
     */
    public function set($index, $value, $force = false)
    {
        // Specal case for the root node: then we're talking about THIS ArrayObject
        if ($index == '.') {
            $this->setRootNode($value, $force);
        }
        else {
            $this->offsetSet($index, $value, $force);
        }
    }
    
    /**
     * Is the given var a hash (associative array)?
     *
     * See http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
     * @param $arr mixed
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
     * @param $index
     * @return bool
     */
    protected function isNullable($index)
    {
        $meta = $this->getMeta($index);
        return (bool)(isset($meta['nullable']) && $meta['nullable']);
    }
    
    /**
     * @param mixed $index
     * @return mixed
     * @throws InvalidLocationException
     */
    public function offsetGet($index)
    {
        //  Remember: isset() returns false if the value is null
        if (empty($this->template) || array_key_exists($index, $this->template)) {
            // This bit allows us to dynamically deepen the object structure
            //if (!isset($this[$index])) {
            if (!array_key_exists($index, $this)) {
                $classname = get_called_class();
                $this->offsetSet($index, new $classname()); // dynamically deepen the object structure just in time
            }
            
            return parent::offsetGet($index); // TODO: Change the autogenerated stub
        } else {
            throw new InvalidLocationException('Index not defined in template: ' . $index);
        }
    }
    
    /**
     * Accessed when the object is written to via array notation
     * @param mixed $index
     * @param mixed $value
     * @param bool $force
     * @throws InvalidDataTypeException
     * @throws InvalidLocationException
     */
    public function offsetSet($index, $value, $force = false)
    {
        // Bypasses filters
        if ($force || empty($this->meta)) {
            print __FUNCTION__ . ':' . __LINE__ . ' (forced or empty meta)' . "\n";
            //print_r($this->meta); print "\n";
            if ($this->isHash($value)) {
                $classname = get_called_class();
                return parent::offsetSet($index, new $classname($value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta)));
            } else {
                return parent::offsetSet($index, $value);
            }
        }
        print __FUNCTION__ . ':' . __LINE__ . ' (unforced, filtered)' . "\n";
        // Index exists in template?
        if (!$this->isValidTargetLocation($index)) {
            throw new InvalidLocationException('Index not valid for writing: ' . $index);
        }
        
        // Filter the value
        $value = $this->filter($value, $index);
        
        parent::offsetSet($index, $value);
    }
    
    
    /**
     * Make sure that the given $index is Ok for writing to.
     * @param $index
     * @throws InvalidLocationException
     * @return boolean
     */
    protected function isValidTargetLocation($index)
    {
        if (empty($this->template)) {
            return true; // No template? No problem!
        }
        
        // TODO: Special rules defined for keys? Override function? Regex?
        
        // Default behavior: check if index exists in template
        if (!array_key_exists($index, $this->template)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Filter the incoming variable at the $index location specified
     * @param $value
     * @param $index
     * @return mixed
     * @throws InvalidDataTypeException
     */
    protected function filter($value, $index)
    {
        $fieldMutator = $this->getMutatorFunctionName($index);
        $typeMutator = $this->getTypeMutatorFunctionName($index);
        $valueMutator = $this->getTypeMutatorFunctionName($index, true); // ???
        
        // Which function should be used to mutate the $value onto the target $index?
        if (method_exists($this, $fieldMutator)) {
            print __FUNCTION__ . ':' . __LINE__ . ' mutate using ' . $fieldMutator . "\n";
            return $this->$fieldMutator($value, $index);
            // TODO: value mutator here?
        } elseif (method_exists($this, $valueMutator)) {
            print __FUNCTION__ . ':' . __LINE__ . ' mutate using ' . $valueMutator . "\n";
            return $this->$valueMutator($value, $index);
        } elseif (method_exists($this, $typeMutator)) {
            print __FUNCTION__ . ':' . __LINE__ . ' mutate using ' . $typeMutator . "\n";
            return $this->$typeMutator($value, $index);
        }
        
        throw new \InvalidArgumentException('No mutator found for index "' . $index . '" (' . $fieldMutator . '? or '.$valueMutator.' or '.$typeMutator.'?)');
    }
    
    /**
     * Get the meta definition data for the given index (normalized or not)
     * @param $index
     * @return array
     */
    protected function getMeta($index)
    {
        $normalized_key = $this->getNormalizedKey($index);
        // No explicit meta data defined for the given index
        if (!isset($this->meta[$normalized_key])) {
            // If there is a global meta definition, use that
            if (isset($this->meta['.'])) {
                return $this->meta['.'];
            }
            // End of the line: no meta data
            return [];
        }
        return $this->meta[$normalized_key];
    }
    
    /**
     * Returns the function name used to mutate values being set at the given $index.  The filter() method will look for
     * a function of this name when modifying values during set operations for the given index (i.e. field).  This takes
     * precedence over the generic fallback functions identified by the getTypeMutatorFunctionName() method.
     * @param $index
     * @return string
     */
    protected function getMutatorFunctionName($index)
    {
        // Append operations will use an index of null, which would output "set"
        return ($index) ? 'set' . ucfirst($index) : null;
    }
    
    /**
     * Returns the function name used to mutate all fields of the given $type.  The filter() method will look for a
     * function of this name when modifying values during set operations.  Type-based mutation only is used if a field does
     * not have a specific mutator function defined (see getMutatorFunctionName()).
     * @param $index string
     * @return string
     */
    protected function getTypeMutatorFunctionName($index, $values = false)
    {
        $normalized_key = $this->getNormalizedKey($index);
        print __FUNCTION__ . ':' . __LINE__ . ' for index "' . $index . '" ('. $normalized_key.")\n";
        
        if ($values) {
            if (isset($this->meta[$normalized_key]['values']['type'])) {
                return 'setType' . ucfirst($this->meta[$normalized_key]['values']['type']);
            }
            // TODO? Look up to the previous one? (not just to '.'
            if (isset($this->meta['.']['values']['type'])) {
                return 'setType' . ucfirst($this->meta['.']['values']['type']);
            }
        }
        if (isset($this->meta[$normalized_key]['type'])) {
            return 'setType' . ucfirst($this->meta[$normalized_key]['type']);
        }
//        if (isset($this->meta[$normalized_key])) {
//            // Special case for the root node
//            if ($normalized_key == '.') {
//                print '     ---> '.__LINE__."\n";
//                if (isset($this->meta[$normalized_key]['values']['type'])) {
//                    print '     ---> '.__LINE__."\n";
//                    return 'setType' . ucfirst($this->meta[$normalized_key]['values']['type']);
//                }
//            }
//            // Look for regular types
//            elseif (isset($this->meta[$normalized_key]['type'])) {
//                print '     ---> '.__LINE__."\n";
//                return 'setType' . ucfirst($this->meta[$normalized_key]['type']);
//            }
//            // Fallback to global values ???
////            elseif (isset($this->meta['.']['values']['type'])) {
////                // If there is a global meta definition, use that
////                return 'setType' . ucfirst($this->meta['.']['values']['type']);
////            }
//
//        }
//        // Fallback to global values
//        elseif (isset($this->meta['.']['values']['type'])) {
//            print '     ---> '.__LINE__."\n";
//            // If there is a global meta definition, use that
//            return 'setType' . ucfirst($this->meta['.']['values']['type']);
//        }
        
        // End of the line: no meta data
        return 'setTypeUnknown';
    }
    
    /**
     * Special case setter used when setting the root node (i.e. the base ArrayObject), used by the constructor
     * @param $input array
     * @param $force boolean
     */
    protected function setRootNode(array $input, $force = false)
    {
        $input = ($input) ? $input : $this->template; // You cannot override $this->template with an empty input
        
        print "-----------------------\n";
        print __FUNCTION__ . ':' . __LINE__ . "\n";
        print_r($input);
        print "\n";
        print_r($this->template);
        print "\n";
        print_r($this->meta);
        print "\n";
        print "-----------------------\n";
        
        
        if ($this->isHash($input)) {
            foreach ($input as $key => $value) {
                //print '    ----> key: '.$key."\n";
                print '    ----> key: ' . $key . ' values: ' . print_r($value, true) . "\n";
                
                $this->offsetSet($key, $value, $force);
            }
        }
        else {
            print __FUNCTION__ . ':' . __LINE__ . " input is array \n";
            foreach ($input as $value) {
                $this->offsetSet(null, $value, $force); // i.e. append
            }
        }
    }
    
    /**
     * Called internally by the filter() method.
     * @param $value
     * @param $index
     * @return mixed
     * @throws InvalidDataTypeException
     */
    protected function setTypeArray($value, $index)
    {
        print __FUNCTION__ . ':' . __LINE__ . ' index="'.$index.'"'."\n";
        if (is_array($value)) {
            $value = array_values($value);
        }
//        elseif ($value instanceof Dto) {
//            $value = array_values($value->toArray());
//        }
        return $this->setTypeHash($value, $index);
    }
    
    /**
     * Called internally by the filter() method.
     * @param $value mixed
     * @param $index string
     * @return bool
     */
    protected function setTypeBoolean($value, $index)
    {
        print __FUNCTION__ . ':' . __LINE__ . ' (index ' . $index . ')' . "\n";
        return (is_null($value) && $this->isNullable($index)) ? null : boolval($value);
    }
    
    /**
     * Called internally by the filter() method.
     * @param $value mixed
     * @param $index string
     * @return int
     * @throws InvalidDataTypeException
     */
    protected function setTypeDto($value, $index)
    {
        print __FUNCTION__ . ':' . __LINE__ . ' (index ' . $index . ')' . "\n";
        
        $meta = $this->getMeta($index);
        
        if (!isset($meta['class'])) {
            throw new \InvalidArgumentException('Meta information for DTO at index ' . $index . ' requires "class" parameter.');
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
        throw new InvalidDataTypeException($index . ' value must be instance of ' . $classname);
    }
    
    /**
     * Called internally by the filter() method.
     * @param $value mixed
     * @param $index string
     * @return float
     */
    protected function setTypeFloat($value, $index)
    {
        print __FUNCTION__ . ':' . __LINE__ . ' (index ' . $index . ')' . "\n";
        return (is_null($value) && $this->isNullable($index)) ? null : floatval($value);
    }
    
    /**
     * Called internally by the filter() method.
     * @param $value mixed
     * @param $index string
     * @return mixed
     * @throws InvalidDataTypeException
     */
    protected function setTypeHash($value, $index)
    {
        print __FUNCTION__ . ':' . __LINE__  . 'with index '.$index."\n";
        $classname = get_called_class();
        
        if (is_null($value)) {
            return ($this->isNullable($index)) ? null : new $classname([], $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
        }
        
        $valueMutator = $this->getTypeMutatorFunctionName($index, true);
        
        // Which function should be used to mutate the $value onto the target $index?
        if (method_exists($this, $valueMutator)) {
            print __FUNCTION__ . ':' . __LINE__ . ' mutate value using ' . $valueMutator . "\n";
            return $this->$valueMutator($value, $index);
        }
        
        throw new \InvalidArgumentException('No value mutator found for index "' . $index . '" (' . $valueMutator . '?');

//        if ($value instanceof Dto) {
//            return $value;
//        }
//
//        if (is_array($value)) {
//            if (empty($value)) {
//                return [];
//            }
//
//            return new $classname($value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
//        }
//
//        // return $value; // ???
//
//
//        throw new InvalidDataTypeException('Cannot write non-array (' . print_r($value, true) . ') to array location @->' . $index);
        // throw new InvalidDataTypeException('Cannot write non-array to array location.');
        
    }
    
    /**
     * Called internally by the filter() method.
     * @param $value mixed
     * @param $index string
     * @return integer
     */
    protected function setTypeInteger($value, $index)
    {
        print __FUNCTION__ . ':' . __LINE__ . ' (index ' . $index . ')' . "\n";
        return (is_null($value) && $this->isNullable($index)) ? null : intval($value);
    }
    
    /**
     * Called internally by the filter() method.
     * @param $value mixed
     * @param $index string
     * @return string
     */
    protected function setTypeScalar($value, $index)
    {
        print __FUNCTION__ . ':' . __LINE__ . ' (index ' . $index . ')' . "\n";
        return (is_null($value) && $this->isNullable($index)) ? null : strval($value);
    }
    
    /**
     * Convenience function (because really, we have better things to do than argue about whether strings are scalars)
     * @param $value
     * @param $index
     * @return string
     */
    protected function setTypeString($value, $index)
    {
        return $this->setTypeScalar($value, $index);
    }
    
    /**
     * Called internally by the filter() method.
     * Type "unknown" is used in cases where:
     *  1. a non-populated index is declared as an array or hash, but without use of the "values" qualifier. (aka ambiguous hash)
     *  2. a template contains nested hashes but it does not have explicit meta data (i.e. the inferred meta data does
     *      not include the "values" quantifier.
     *
     * @param $value mixed
     * @param $index string
     * @return mixed
     * @throws InvalidDataTypeException
     */
    protected function setTypeUnknown($value, $index)
    {
        
        print __FUNCTION__ . ':' . __LINE__ . ' for index ' . $index . "\n";
        //$meta = $this->getMeta($index); // double-check this? getMutatorType already directed us here
        $classname = get_called_class();
        
        $value = ($value instanceof Dto) ? $value->toArray() : $value;
        
        // Ensure child arrays are converted to Dto
        if (is_array($value)) {
            return new $classname($value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
        }
        
        return $value;
        
    }
    
    /**
     * The input allows the injection of a foreign Dto $arrayObj for recursive resolving of children DTOs.
     * @param Dto|null $arrayObj
     * @return array
     */
    public function toArray(Dto $arrayObj = null)
    {
        $arrayObj = ($arrayObj) ? $arrayObj : $this;
        $output = [];
        foreach ($arrayObj as $k => $v) {
            if ($v instanceof Dto) {
                $output[$k] = $this->toArray($v);
            } else {
                $output[$k] = $v;
            }
        }
        
        return $output;
    }
    
    /**
     * Convert the specified arrayObj to JSON.  Ultimately, this is a decorator around the toArray() method.
     * @param boolean $pretty
     * @param Dto $arrayObj
     * @return string
     */
    public function toJson($pretty = false, Dto $arrayObj = null)
    {
        return ($pretty) ? json_encode($this->toArray($arrayObj), JSON_PRETTY_PRINT) : json_encode($this->toArray($arrayObj));
    }
    
    /**
     * Convert the specified arrayObj to a stdClass object.  Ultimately, this is a decorator around the toJson() method.
     * @param Dto $arrayObj
     * @return object stdClass
     */
    public function toObject(Dto $arrayObj = null)
    {
        return json_decode($this->toJson(false, $arrayObj));
    }
}
