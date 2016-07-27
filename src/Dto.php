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
class Dto extends \ArrayObject {

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
        $arg_list = func_get_args();
        //print_r($arg_list); exit;
//        print "-----------------------\n";
//        print __FUNCTION__.':'.__LINE__."\n";
//        print_r($input); print "\n";
//        print_r($template); print "\n";
//        print_r($meta); print "\n";
//        print "-----------------------\n";

        // We need to be able to override the class variables, especially when the input variables are empty.
        // This pattern won't work:
        //      $this->template = ($template) ? $template : $this->template;
        // It doesn't work because it triggers a loop condition:  $this->template will always be used as a fallback
        // when the input $template is empty.  Instead, we use func_get_args to detect if the input was set, and if set,
        // that input overrides any pre-defined class level variable.  It's necessary to force the class level variables
        // to empty values before we dive into auto-detection and offsetSet.
        $this->template = (isset($arg_list[1])) ? $arg_list[1] : $this->template;
        $this->meta = (isset($arg_list[2])) ? $arg_list[2] : $this->meta;
        $this->meta = $this->normalizeMeta($this->meta);
        $this->meta = $this->autoDetectTypes($this->template, $this->meta);

        $input = ($input) ? $input : $this->template; // You cannot override $this->template with an empty input

        print "-----------------------\n";
        print __FUNCTION__.':'.__LINE__."\n";
        print_r($input); print "\n";
        print_r($this->template); print "\n";
        print_r($this->meta); print "\n";
        print "-----------------------\n";

        $this->setFlags(0);

        foreach ($input as $key => $value) {
            //print '    ----> key: '.$key."\n";
            print '    ----> key: '.$key.' '.print_r($value,true)."\n";
            $this->offsetSet($key, $value);
        }

        //print_r($this->meta); exit;
    }

    /**
     * Append a value to the end of an array.  Defers to offsetSet to determine if location is valid for appending.
     * See http://php.net/manual/en/arrayobject.append.php
     * @param mixed $val
     */
    public function append($val) {
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
        print __FUNCTION__.':'.__LINE__."\n";

        foreach ($template as $index => $v) {

            $meta_key = $this->getNormalizedKey($index);

            if (!is_array($template[$index])){
                if (is_bool($template[$index])) {
                    $meta[$meta_key]['type'] = 'boolean';
                } elseif (is_int($template[$index])) {
                    $meta[$meta_key]['type'] = 'integer';
                } elseif (is_numeric($template[$index])) {
                    $meta[$meta_key]['type'] = 'float';
                } elseif (is_scalar($template[$index])) {
                    $meta[$meta_key]['type'] = 'scalar';
                }
            }
            // Hashes
            elseif($this->isHash($template[$index])) {
                $meta[$meta_key]['type'] = 'hash';
            }
            // Arrays
            elseif(is_array($template[$index])) {
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
        //print __FUNCTION__.':'.__LINE__."\n";
        return '.'.trim($key,'.');
    }
    
    /**
     * @param $key
     * @return bool
     */
    protected function isValidMetaKey($key)
    {
        //print __FUNCTION__.':'.__LINE__."\n";
        if (!is_scalar($key)) {
            return false;
        }
        //if ($key == '.' || $key == '') {
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
        // print __FUNCTION__.':'.__LINE__."\n";
        $trimmed = [];
        $prefix = $this->getNormalizedKey($prefix);

        foreach ($meta as $dotted_key => $value) {
            if (substr($dotted_key, 0, strlen($prefix)) == $prefix) {
                // shift something like ".foo.bar" to ".bar"
                if ($new_key = substr($dotted_key, strlen($prefix))) {
                    $trimmed[$new_key] = $value;
                }
                // shift something like ".foo" to "."
                else {
                    $trimmed['.'] = $value;
                }
            }
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
        // print __FUNCTION__.':'.__LINE__."\n";
        if (isset($template[$index]) && is_array($template[$index])) {
            return $template[$index];
        }

        return [];
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
        // print __FUNCTION__.':'.__LINE__."\n";
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
     * This helps remind the user that they tried to access the ArrayObject in the wrong context.
     * 
     * @return string
     */
    public function __toString()
    {
        print __FUNCTION__.':'.__LINE__."\n";
        return 'Array';
    }


    /**
     * NOTE: Only get used if ARRAY_AS_PROPS is NOT set
     * This does get called if STD_PROP_LIST is set
     * but then the value doesn't stick
     * Accessed when the object is written to via object notation.
     *
     * @param $name
     * @param $value
     * @throws InvalidLocationException
     */
    public function __set($name, $value) {
        //print __FUNCTION__.':'.__LINE__."\n";
        return $this->offsetSet($name, $value);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name) {
        //print __FUNCTION__.':'.__LINE__.' '.print $name."\n";
        //print __FUNCTION__.':'.__LINE__."\n";
        //print_r($this[$name]);
        //print "\n";
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
        // print __FUNCTION__.':'.__LINE__."\n";
        $parts = explode('.', trim($dotted_key,'.'));

        $location = $this->{array_shift($parts)}; // prime the pump with the first location

        foreach($parts as $k) {
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
        print __FUNCTION__.':'.__LINE__."\n";
        $this->offsetSet($index, $value, $force);
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
        // print __FUNCTION__.':'.__LINE__."\n";
        if (!is_array($arr) || empty($arr)) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @param mixed $index
     * @return mixed
     * @throws InvalidLocationException
     */
    public function offsetGet($index)
    {
        print __FUNCTION__.':'.__LINE__.' '.$index."\n";
        //  Remember: isset() returns false if the value is null
        if (empty($this->template) || array_key_exists($index, $this->template)) {
            // This bit allows us to dynamically deepen the object structure
            if (!isset($this[$index])) {
                $classname = get_called_class();
                $this->offsetSet($index, new $classname()); // dynamically deepen the object structure just in time
            }

            return parent::offsetGet($index); // TODO: Change the autogenerated stub
        }
        else {
            throw new InvalidLocationException('Index not defined in template: '.$index);
        }
    }

    // Accessed when the object is written to via array notation
    /**
     * @param mixed $index
     * @param mixed $value
     * @param bool $force
     * @throws InvalidDataTypeException
     * @throws InvalidLocationException
     */
    public function offsetSet($index, $value, $force = false)
    {

        // Bypasses filters
        //if ($force || empty($this->template)) {
        if ($force || empty($this->meta)) {
            print __FUNCTION__.':'.__LINE__.' (forced or empty meta)'."\n";
            //print_r($this->meta); print "\n";
            if ($this->isHash($value)) {
                $classname = get_called_class();
                return parent::offsetSet($index, new $classname($value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta)));
            }
            else {
                return parent::offsetSet($index, $value);
            }
        }
        print __FUNCTION__.':'.__LINE__.' (unforced, filtered)'."\n";
        // Index exists in template?
        if (!$this->isValidTargetLocation($index)) {
            throw new InvalidLocationException('Index not valid for writing: '.$index);
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
        //$meta = $this->getMeta($index);

        $type = $this->getMutatorType($index);
        print __FUNCTION__.':'.__LINE__.' getMutatorType '.print_r($type,true)."\n";
        $mutator = $this->getMutatorFunctionName($index);
        $typeMutator = $this->getTypeMutatorFunctionName($type);

        // Which function should be used to mutate the $value onto the target $index? 
        if (method_exists($this, $mutator)) {
            print __FUNCTION__.':'.__LINE__.' mutate using '.$mutator."\n";
            return $this->$mutator($value, $index);
        }
        elseif (method_exists($this, $typeMutator)) {
            print __FUNCTION__.':'.__LINE__.' mutate using '.$typeMutator."\n";
            return $this->$typeMutator($value, $index);
        }

        throw new \InvalidArgumentException('No mutator found for index "'.$index. '" ('.$mutator.'?) or type "'. $type.'" ('.$typeMutator.'?)');
    }

    /**
     * @param $meta array meta data for a single field
     * @return mixed
     */
    // protected function getMutatorType(array $meta)
    protected function getMutatorType($index)
    {
        print __FUNCTION__.':'.__LINE__.' for index "'.$index."\"\n";
        $normalized_key = $this->getNormalizedKey($index);
        // No explicit meta data defined for the given index
        if (!isset($this->meta[$normalized_key])) {
            // If there is a global meta definition, use that
            if (isset($this->meta['.']['values'])) {
                return $this->meta['.']['values'];
            }
            // End of the line: no meta data
            return 'unknown';
        }
        
        // Index is null for appending to arrays
        if (is_null($index)) {
            if (isset($this->meta[$normalized_key]['values'])) {
                return $this->meta[$normalized_key]['values'];
            }
            else {
                return 'unknown';
            }
        }
        
        return $this->meta[$normalized_key]['type'];

        // ... OLD ...
        // $meta data for a given index should always have "type" set
        $type = $meta['type'];
        // Array and Hash types can use an alternate mutator for their members
//        if (in_array($type, ['array', 'hash'])) {
//        //if (in_array($type, ['hash'])) {
//            if (isset($meta['values'])) {
//                $type = $meta['values'];
//            }
//            else {
//                $type = 'unknown';
//            }
//        }
        return $type;
    }

    /**
     * Get the meta definition data for the given index (normalized or not)
     * @param $index
     * @return array
     */
    protected function getMeta($index)
    {
        // print __FUNCTION__.':'.__LINE__."\n";
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
        return ($index) ? 'set'.ucfirst($index) : null;
    }

    /**
     * Returns the function name used to mutate all fields of the given $type.  The filter() method will look for a
     * function of this name when modifying values during set operations.  Type-based mutation only is used if a field does
     * not have a specific mutator function defined (see getMutatorFunctionName()).
     * @param $type string
     * @return string
     */
    protected function getTypeMutatorFunctionName($type)
    {
        // print __FUNCTION__.':'.__LINE__.' setType'.ucfirst($type)."\n";
        return 'setType'.ucfirst($type);
    }

    /**
     * Called internally by the filter() method.
     * @param $value mixed
     * @return bool
     */
    protected function setTypeBoolean($value)
    {
        print __FUNCTION__.':'.__LINE__."\n";
        return boolval($value);
    }

    /**
     * Called internally by the filter() method.
     * @param $value mixed
     * @return integer
     */
    protected function setTypeInteger($value)
    {
        print __FUNCTION__.':'.__LINE__."\n";
        return intval($value);
    }

    /**
     * Called internally by the filter() method.
     * @param $value mixed
     * @return float
     */
    protected function setTypeFloat($value)
    {
        print __FUNCTION__.':'.__LINE__."\n";
        return floatval($value);
    }

    /**
     * Called internally by the filter() method.
     * @param $value mixed
     * @return string
     */
    protected function setTypeScalar($value)
    {
        print __FUNCTION__.':'.__LINE__."\n";
        // TODO? throw Exception for non-scalar types?
        return strval($value);
    }
    
    /**
     * Convenience function (because really, we have better things to do than argue about whether strings are scalars)
     * @param $value
     * @return string
     */
    protected function setTypeString($value) {
        return $this->setTypeScalar($value);
    }
    
    /**
     * Called internally by the filter() method.
     * @param $value
     * @param $index
     * @return mixed
     * @throws InvalidDataTypeException
     */
    protected function setTypeHash($value, $index)
    {
        $classname = get_called_class();

        // TODO: is_nullable?
        if (is_null($value)) {
            print __FUNCTION__.':'.__LINE__.' (null)'."\n";
            //return [];
            //print __FUNCTION__.':'.__LINE__.' null '."\n";
            // I.e. an empty hash
            return new $classname([], $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
        }

        if ($value instanceof Dto) {
            print __FUNCTION__.':'.__LINE__.' (dto)'."\n";
            //return $value;
            $value = $value->toArray();
            //return new $classname($value->toArray(), $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
        }

        if (is_array($value)) {
            print __FUNCTION__.':'.__LINE__.' (array)'."\n";
            $meta = $this->getMeta($index);
            if (isset($meta['values'])) {
                $typeMutator = $this->getTypeMutatorFunctionName($meta['values']);
                foreach ($value as $k => $v) {
                    if (method_exists($this, $typeMutator)) {
                        $value[$k] = $this->$typeMutator($v, $k);
                    }
                }
            }
            //return $value;
            return new $classname($value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
        }

        
        throw new InvalidDataTypeException('Cannot write non-array ('.print_r($value,true).') to array location @->'.$index);
        // throw new InvalidDataTypeException('Cannot write non-array to array location.');

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
        print __FUNCTION__.':'.__LINE__."\n";
        if (is_array($value)) {
            $value = array_values($value);
        }
        elseif ($value instanceof Dto) {
            $value = array_values($value->toArray());
        }
        return $this->setTypeHash($value, $index);
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
    protected function setTypeUnknown($value, $index) {

        print __FUNCTION__.':'.__LINE__.' for index '.$index."\n";
        $meta = $this->getMeta($index); // double-check this? getMutatorType already directed us here
        $classname = get_called_class();

        if ($value instanceof Dto) {
            $value = $value->toArray();
        }
        // Ensure child arrays are converted to Dto
        if (is_array($value)) {
            if ($meta['type'] == 'array') {
                $value = array_values($value);
            }
            return new $classname($value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
        }
        elseif ($meta['type'] == 'array') {
            // throw new InvalidDataTypeException('Cannot write non-array to array location.');
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
        //print __FUNCTION__.':'.__LINE__."\n";
        $arrayObj = ($arrayObj) ? $arrayObj : $this;
        $output = [];
        foreach ($arrayObj as $k => $v) {
            if ($v instanceof Dto) {
                $output[$k] = $this->toArray($v);
            }
            else {
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
        if ($pretty) {
            return json_encode($this->toArray($arrayObj), JSON_PRETTY_PRINT);
        }
        return json_encode($this->toArray($arrayObj));
    }

    /**
     * Convert the specified arrayObj to a StdClass object.  Ultimately, this is a decorator around the toJson() method.
     * @param Dto $arrayObj
     * @return object
     */
    public function toObject(Dto $arrayObj = null)
    {
        return json_decode($this->toJson(false, $arrayObj));
    }
}
