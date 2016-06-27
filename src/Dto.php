<?php
namespace Dto;

use Dto\Exceptions\InvalidDataTypeException;
use Dto\Exceptions\InvalidLocationException;
use Dto\Exceptions\InvalidMetaKeyException;

/**
 * Class Dto
 *
 * See http://php.net/manual/en/class.arrayobject.php  ?
 * See https://symfony.com/doc/current/components/property_access/introduction.html#installation
 */
class Dto extends \ArrayObject {

    protected $template = [];
    protected $meta = [];

    protected $log = true;

    public function log($str) {
        if ($this->log) {
            print $str ."\n";
        }
    }

    /**
     * Dto constructor.
     *
     * @param array $input starter data, filtered against the $template and $meta (if supplied)
     * @param array $template generic data template (i.e. default values) with loosely typed values
     * @param array $meta definitions
     */
    public function __construct(array $input = [], array $template = [], array $meta = [])
    {
        $this->template = ($this->template) ? $this->template : $template;
        $this->meta = ($this->meta) ? $this->meta : $meta;
        $this->meta = $this->normalizeMeta($this->meta);

        $this->meta = $this->autoDetectTypes($this->template, $this->meta);
        //print_r($this->meta); exit;
        $input = ($input) ? $input : $this->template;

        $this->setFlags(0);

        // Filter values here?
//        foreach ($input as $key => $value) {
////            $normalized_key = $this->getNormalizedKey($key);
////            $meta = $this->meta[$normalized_key];
////            print 'here:'; print_r($meta); exit;
//            $this->offsetSet($key, $value);
//
//        }

        foreach ($input as $key => $value) {
            //if (is_array($value) && !empty($value)) {
            if (is_array($value)) {
                //$this->offsetSet($key, new $class($value, $this->flags, $this->iteratorClass));
                $template_subset = (isset($this->template[$key])) ? $this->template[$key] : [];
                $classname = get_called_class();
                $this->offsetSet($key, new $classname($value, $template_subset, $this->getMetaSubset($key, $this->meta)));
            }
            else {
                $this->offsetSet($key, $value);
            }
        }

        $this->log(sprintf("%s(%s) completed", __FUNCTION__, implode(", ", func_get_args())));


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

            if (!is_array($template[$index])){

                if (is_bool($template[$index])) {
                    $meta[$meta_key]['type'] = 'boolean';
                    $meta[$meta_key]['callback'] = function ($value) {
                        return boolval($value);
                    };
                } elseif (is_int($template[$index])) {

                    $meta[$meta_key]['type'] = 'integer';
                    $meta[$meta_key]['callback'] = function ($value) {
                        return intval($value);
                    };
                } elseif (is_numeric($template[$index])) {
                    $meta[$meta_key]['type'] = 'float';
                    $meta[$meta_key]['callback'] = function ($value) {
                        return floatval($value);
                    };
                } elseif (is_scalar($template[$index])) {
                    $meta[$meta_key]['type'] = 'scalar';
                    $meta[$meta_key]['callback'] = function ($value) {
                        return strval($value);
                    };
                }
            }
            // Hashes
            elseif($this->isHash($template[$index])) {
                // TODO
                // print $index; exit;
            }
            // Arrays
            elseif(is_array($template[$index])) {
                $meta[$meta_key]['type'] = 'array';
                $meta[$meta_key]['callback'] = function ($value, $template, $meta) {
                    if ($value instanceof \Dto\Dto) {
                        // Re-index the array -- make this as close to a "real" array as possible in PHP.
                        $value = array_values($value->toArray());
                        $classname = get_called_class();
                        return new $classname($value, $template, $meta);
                    }
                    else {
                        throw new InvalidDataTypeException('Cannot write non-array to array location.');
                    }
                };
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
        return '.'.trim($key,'.');
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
     * prefix removed.
     * 
     * @param $prefix string
     * @param $meta array
     * @return array
     */
    protected function getMetaSubset($prefix, array $meta)
    {
        $trimmed = [];
        $prefix = $this->getNormalizedKey($prefix);
        //print '-->'. $prefix; exit;
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
        // For arrays/non-scalar values, we create a child object at the location indicated
        if (is_array($value)) {
        //if (is_array($value) && !empty($value)) { // causes segmentation fault
            $this->log(sprintf("New DTO: %s(%s)", __FUNCTION__, implode(", ", func_get_args())));
            $classname = get_called_class();
            $this->offsetSet($name, new $classname($value, $this->getTemplateSubset($name, $this->template), $this->getMetaSubset($name, $this->meta)));
            //$this->offsetSet($name, new \Dto\Dto($value, $this->getTemplateSubset($name, $this->template), $this->getMetaSubset($name, $this->meta)));
        }
        else {
            $this->log(sprintf("Boring %s(%s)", __FUNCTION__, implode(", ", func_get_args())));
            $this[$name] = $value; // routes to offsetSet?
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name) {
        $this->log(sprintf("%s(%s)", __FUNCTION__, implode(", ", func_get_args())));
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
        // This bit allows us to dynamically deepen the object structure
        if (!isset($this[$index])) {
            $this->log(sprintf("DYNAMIC @ $index %s(%s)", __FUNCTION__, implode(", ", func_get_args())));
            $classname = get_called_class();
            $this->offsetSet($index, new $classname());
        }

        $this->log(sprintf("%s(%s)", __FUNCTION__, implode(", ", func_get_args())));
        return parent::offsetGet($index); // TODO: Change the autogenerated stub
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
        $this->log(sprintf("%s(%s, ...)", __FUNCTION__, $index));

        // No filters applied
        //if ($force || empty($value) || empty($this->template)) {
        if ($force || empty($this->template)) {
            $this->log(sprintf("Line %s - index %s", __LINE__, $index));
            return parent::offsetSet($index, $value);
        }

//        if (empty($value)) {
//            //print 'Empty @ '.$index; exit;
//            $this[$index] = []; // Segmentation fault
//            //$this[$index] = $value; // Segmentation fault
//            return;
//            return $this->__set($index, $value);
//        }

        if (is_array($value)) {
            // convert incoming values into arrayObjects?
            $this->log(sprintf("New DTO: %s(%s)", __FUNCTION__, implode(", ", func_get_args())));
            $classname = get_called_class();
            $this->offsetSet($index, new $classname($value, $this->getTemplateSubset($index, $this->template), $this->getMetaSubset($index,$this->meta)));

        }
        else {
            // PHP gotcha: isset comes back false if the value is set to null
            //if (!isset($this->template[$index])) {
            if (!array_key_exists($index, $this->template)) {
                //print $this->getNormalizedKey($index); exit; // 0
                //print_r($this->meta); exit;
                //print 'META: ' . $this->meta[$this->getNormalizedKey($index)]['type']; exit;

                throw new InvalidLocationException(sprintf(
                    'Location "%s" not defined in %s::$template ',
                    $index,
                    get_class($this)
                ));
            }

            // TODO: FILTER?
            // Detect type @ $index
            // Filter value
            $value = $this->filter($value, $index);
            if ($value instanceof \Dto\Dto) {
                $this->log(sprintf("Boring Dto %s(%s)", __FUNCTION__, $index.', Dto'));
            }
            else {
                $this->log(sprintf("Boring %s(%s)", __FUNCTION__, implode(", ", func_get_args())));
            }
            //return parent::offsetSet($index, 'dinky');
            return parent::offsetSet($index, $value);
        }
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
        $normalized_key = $this->getNormalizedKey($index);

        $mutator = $this->getMutatorFunctionName($index);
        if (method_exists($this, $mutator)) {
            return $this->$mutator($value, $this->template, $this->meta);
        }
        elseif (isset($this->meta[$normalized_key]['callback'])) {
            return call_user_func($this->meta[$normalized_key]['callback'], $value, $this->template, $this->meta);
        }

        throw new \InvalidArgumentException('No callback or mutator found for index '.$index);
    }

    protected function getMutatorFunctionName($index)
    {
        return 'set'.$index;
    }

    /**
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
