<?php
namespace Dto;

use Dto\Exceptions\InvalidDataTypeException;
use Dto\Exceptions\InvalidLocationException;
use Dto\Exceptions\InvalidMetaKeyException;

/**
 * Class Dto (Data Transfer Object)
 *
 * See http://php.net/manual/en/class.arrayobject.php  ?
 * See https://symfony.com/doc/current/components/property_access/introduction.html#installation
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
        //$this->template = ($this->template) ? $this->template : $template;
        // We need to be able to override the class variables, esp. if the input variables are empty
        $this->template = (isset($arg_list[1])) ? $arg_list[1] : $this->template;
        $this->meta = (isset($arg_list[2])) ? $arg_list[2] : $this->meta;
        $this->meta = $this->normalizeMeta($this->meta);
        $this->meta = $this->autoDetectTypes($this->template, $this->meta);

        $input = ($input) ? $input : $this->template;

        print "-----------------------\n";
        print __FUNCTION__.':'.__LINE__."\n";
        print_r($input); print "\n";
        print_r($this->template); print "\n";
        print_r($this->meta); print "\n";
        print "-----------------------\n";

        $this->setFlags(0);

        foreach ($input as $key => $value) {
            print '    ----> key: '.$key."\n";
            $this->offsetSet($key, $value);
        }

        //print_r($this->meta); exit;
    }
    
    protected function getInput($input, $template)
    {
        
    }

    /**
     * See http://php.net/manual/en/arrayobject.append.php
     * @param mixed $v
     */
    public function append($v) {
        return $this->offsetSet(null, $v);
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
        print __FUNCTION__.':'.__LINE__."\n";
        return '.'.trim($key,'.');
    }
    
    /**
     * @param $key
     * @return bool
     */
    protected function isValidMetaKey($key)
    {
        print __FUNCTION__.':'.__LINE__."\n";
        if (!is_scalar($key)) {
            return false;
        }
        if ($key == '.' || $key == '') {
            return false;
        }
        if (strpos($key, '..') !== false) {
            return false;
        }

        return true;
    }
    
    /**
     * Returns the part of the supplied $meta array whose keys begin with the $prefix and re-index the array with the 
     * prefix removed.  This is used to get a subset of the meta data when instantiating a child class.
     * 
     * @param $prefix string
     * @param $meta array
     * @return array
     */
    protected function getMetaSubset($prefix, array $meta)
    {
        print __FUNCTION__.':'.__LINE__."\n";
        $trimmed = [];
        $prefix = $this->getNormalizedKey($prefix);

        foreach ($meta as $dotted_key => $value) {
            
            if (substr($dotted_key, 0, strlen($prefix)) == $prefix) {
                if ($new_key = substr($dotted_key, strlen($prefix))) {
                    $trimmed[$new_key] = $value;
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
        print __FUNCTION__.':'.__LINE__."\n";
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
        print __FUNCTION__.':'.__LINE__."\n";
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
        print __FUNCTION__.':'.__LINE__."\n";
        return $this->offsetSet($name, $value);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name) {
        //print __FUNCTION__.':'.__LINE__.' '.print $name."\n";
        print __FUNCTION__.':'.__LINE__."\n";
        print_r($this[$name]);
        print "\n";
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
        print __FUNCTION__.':'.__LINE__."\n";
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
        print __FUNCTION__.':'.__LINE__."\n";
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
                $this->offsetSet($index, new $classname());
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
        print __FUNCTION__.':'.__LINE__."\n";
        // Bypasses filters
        if ($force || empty($this->template)) {
            if ($this->isHash($value)) {
                $classname = get_called_class();
                return parent::offsetSet($index, new $classname($value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta)));
            }
            else {
                return parent::offsetSet($index, $value);
            }
        }

        // Filter the value
        $value = $this->filter($value, $index);
        parent::offsetSet($index, $value);

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
        print __FUNCTION__.':'.__LINE__."\n";
        $normalized_key = $this->getNormalizedKey($index);

        $type = $this->meta[$normalized_key]['type'];

        $mutator = $this->getMutatorFunctionName($index);
        $typeMutator = $this->getTypeMutatorFunctionName($type);

        if (method_exists($this, $mutator)) {
            return $this->$mutator($value, $index);
        }
        elseif (method_exists($this, $typeMutator)) {
            return $this->$typeMutator($value, $index);
        }

        throw new \InvalidArgumentException('No mutator found for index "'.$index. '" ('.$mutator.'?) or type "'. $type.'" ('.$typeMutator.'?)');
    }

    /**
     * Get the meta definition for the given index
     * @param $index
     * @return array
     * @throws InvalidMetaKeyException
     */
    protected function getMeta($index)
    {
        print __FUNCTION__.':'.__LINE__."\n";
        $normalized_key = $this->getNormalizedKey($index);
        if (!isset($this->meta[$normalized_key])) {
            throw new InvalidMetaKeyException('No meta data defined for index "'.$normalized_key.'"');
        }
        return $this->meta[$normalized_key];
    }

    protected function getMutatorFunctionName($index)
    {
        print __FUNCTION__.':'.__LINE__."\n";
        return 'set'.ucfirst($index);
    }

    /**
     * @param $type string
     * @return string
     */
    protected function getTypeMutatorFunctionName($type)
    {
        print __FUNCTION__.':'.__LINE__."\n";
        return 'setType'.ucfirst($type);
    }

    /**
     * @param $value mixed
     * @return bool
     */
    protected function setTypeBoolean($value)
    {
        print __FUNCTION__.':'.__LINE__."\n";
        return boolval($value);
    }

    /**
     * @param $value mixed
     * @return integer
     */
    protected function setTypeInteger($value)
    {
        print __FUNCTION__.':'.__LINE__."\n";
        return intval($value);
    }

    /**
     * @param $value mixed
     * @return integer
     */
    protected function setTypeFloat($value)
    {
        print __FUNCTION__.':'.__LINE__."\n";
        return floatval($value);
    }

    /**
     * @param $value mixed
     * @return integer
     */
    protected function setTypeScalar($value)
    {
        print __FUNCTION__.':'.__LINE__."\n";
        // TODO? throw Exception for non-scalar types?
        return strval($value);
    }

    /**
     * @param $value
     * @param $index
     * @return mixed
     */
    protected function setTypeHash($value, $index)
    {
        $classname = get_called_class();

        if (is_null($value)) {
            print __FUNCTION__.':'.__LINE__.' (null)'."\n";
            //return [];
            //print __FUNCTION__.':'.__LINE__.' null '."\n";
            // This sends stuff into a loop
            return new $classname([], $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
        }

        if ($value instanceof Dto) {
            print __FUNCTION__.':'.__LINE__.' (dto)'."\n";
            //return $value;
            return new $classname($value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
        }
        elseif (is_array($value)) {
            print __FUNCTION__.':'.__LINE__.' (array)'."\n";
            //return $value;
            return new $classname($value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
        }
        else {
            print __FUNCTION__.':'.__LINE__.' (other)'."\n";
            //return (array) $value;
            return new $classname((array) $value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
            // Why must the array's value be a DTO?
            //throw new InvalidDataTypeException('Cannot write non-array to array location.');
        }

//        print __FUNCTION__.':'.__LINE__."\n";
//        if (is_null($value)) {
//            return [];
//        }
//
//        $classname = get_called_class();
//
//        if ($value instanceof Dto) {
//            return new $classname($value->toArray(), $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
//        }
//        else {
//            return new $classname($value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
//            //return new $classname((array) $value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
//        }
    }

    /**
     * @param $value
     * @param $index
     * @return mixed
     * @throws InvalidDataTypeException
     */
    protected function setTypeArray($value, $index)
    {

        $classname = get_called_class();

        if (is_null($value)) {
            print __FUNCTION__.':'.__LINE__.' (null)'."\n";
            //return [];
            //print __FUNCTION__.':'.__LINE__.' null '."\n";
            // This sends stuff into a loop
            return new $classname([], $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
        }

        if ($value instanceof Dto) {
            print __FUNCTION__.':'.__LINE__.' (dto)'."\n";
            // Re-index the array -- make this as close to a "real" array as possible in PHP.
            $value = array_values($value->toArray());
            //return $value;
            return new $classname($value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
        }
        elseif (is_array($value)) {
            print __FUNCTION__.':'.__LINE__.' (array)'."\n";
            $value = array_values($value);
            //return $value;
            return new $classname($value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
        }
        else {
            print __FUNCTION__.':'.__LINE__.' (other)'."\n";
            //return (array) $value;
            return new $classname((array) $value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index, $this->meta));
            // Why must the array's value be a DTO?
            //throw new InvalidDataTypeException('Cannot write non-array to array location.');
        }
    }

    /**
     * @param Dto|null $arrayObj
     * @return array
     */
    public function toArray(Dto $arrayObj = null)
    {
        print __FUNCTION__.':'.__LINE__."\n";
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
     * Convert the specified arrayObj to JSON
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
     * Convert the specified arrayObj to a StdClass object
     * @param Dto $arrayObj
     * @return object
     */
    public function toObject(Dto $arrayObj = null)
    {
        return json_decode($this->toJson(false, $arrayObj));
    }


}
