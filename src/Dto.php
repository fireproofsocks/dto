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
class Dto extends \ArrayObject implements DtoInterface
{
    /**
     * I.e. the "$schema" keyword, an "absolute" URI
     *
     * @var string
     */
    //protected $schema = 'http://json-schema.org/draft-04/schema#';
    protected $schema;

    /**
     * URI for the schema
     * @var string
     */
    protected $id = '';

    protected $title = '';

    /**
     * Human readable description of your schema
     * @var string
     */
    protected $description = '';

    /**
     * Defines one type for the data (e.g. "object"), or a list of allowable types (e.g. ["array", "null"])
     * string | array
     * @var string
     */
    protected $type = '';

    /**
     * Only relevant when type=object
     * @var array
     */
    protected $properties = [];

    /**
     * List of required properties
     * @var array
     */
    protected $required = [];

    /**
     * Only relevant when type=array, this defines what each array element looks like
     * @var array
     */
    protected $items = [];

    /**
     * In-line schema definitions
     * @var array
     */
    protected $definitions = [];


    protected $patternProperties = [];

    /**
     * Can additional ad-hoc properties be added? (Formerly refered to as "ambiguous hash")
     * @var bool
     */
    protected $additionalProperties = false;

    /**
     * Only relevant when type is not an aggregate (i.e. when type is neither object nor array)
     * @var
     */
    protected $scalarValue;


    // use Trait?  w $default_schema = [...]
    // $this->my_schema = [...];
    // $this->schema = new Schema(array_merge($this->default_schema, $this->my_schema));
    // Default values?
    /**
     * Dto constructor.
     *
     * @param mixed $input values filtered against the $template and $meta
     * @param $schema JsonSchema|null
     */
    public function __construct($input = null, JsonSchema $schema = null)
    {
        $this->setFlags(0);

        $this->schema = ($schema) ? $schema : new JsonSchema();

        // Filter input
        if ($input === null) {
            parent::__construct();
        }
        else {
            parent::__construct($input);
        }
//        $arg_list = func_get_args();
//
//        // We need to be able to override the class variables when the input variables are empty.
//        $this->template = (isset($arg_list[1])) ? $arg_list[1] : $this->template;
//        $this->meta = (isset($arg_list[2])) ? $arg_list[2] : $this->meta;
//        $this->meta = $this->autoDetectTypes($this->template,
//            $this->normalizeMeta($this->meta));
//        // We must always ensure that the template's properties are passed to filterRoot
//        $input = array_replace_recursive($this->template, $input);
//        // store the filtered values in the ArrayObject
//        parent::__construct($this->filterRoot($input));
    }

    /**
     * Is the object in a valid state?
     */
    public function isValid()
    {
        // TODO
    }

    /**
     * Used for object notation, e.g. print $dto->foo
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
     * Alternative setter: Expose the $bypass flag to skip any checks/data-typing
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
     *
     *
     * @param mixed $index
     * @param mixed $newval
     * @param bool $bypass filters if true
     *
     * @throws AppendException
     * @throws InvalidLocationException
     */
    final public function offsetSet($index, $newval, $bypass = false)
    {
        if ($bypass || $this->isPropertySettable($index)) {
            parent::offsetSet($index, $newval);
        }

    }


    /**
     * Either the property is explicitly defined in the $properties array, or $additionalProperties is set to true.
     * @param $index
     * @return boolean
     */
    protected function isPropertySettable($index)
    {
        return ($this->additionalProperties || array_key_exists($index, $this->properties));
    }

    /**
     * @param $value mixed
     * @return bool
     */
    protected function isValueOneOfAllowedTypes($value)
    {
        // TODO: enum could be up here?

        // Type is usually a single value, but some may allow multiple types, esp. nullable, e.g. ["string", "null"]
        $types = (is_array($this->type)) ? $this->type : [$this->type];

        foreach ($types as $t) {
            switch ($t) {
                case 'object':
                    if ($this->isObject($value)) {
                        return true;
                    }
                    break;
                case 'array':
                    if ($this->isArray($value)) {
                        return true;
                    }
                    break;
                case 'string':
                    if ($this->isString($value)) {
                        return true;
                    }
                    break;
                case 'integer':
                    if ($this->isInteger($value)) {
                        return true;
                    }
                    break;
                case 'number':
                    if ($this->isNumber($value)) {
                        return true;
                    }
                    break;
                case 'boolean':
                    if ($this->isBoolean($value)) {
                        return true;
                    }
                    break;
                case 'null':
                    if ($this->isNull($value)) {
                        return true;
                    }
                    break;
            }
        }

        return false;
    }

    protected function isObject($value)
    {
        return (is_object($value));
    }

    protected function isArray($value)
    {
        return $this->isTrueArray($value);
    }

    protected function isString($value)
    {
        return (is_string($value));
    }

    protected function isInteger($value)
    {
        return (is_integer($value));
    }

    protected function isNumber($value)
    {
        // Fall back to integers
        return (is_float($value)) ? true : $this->isInteger($value);
    }

    protected function isBoolean($value)
    {
        return (is_bool($value));
    }

    protected function isNull($value)
    {
        return ($value === null);
    }

    /**
     * Helps us work around one of PHP's warts: there are no true arrays in PHP
     * http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
     * @param $value mixed
     * @return bool
     */
    protected function isTrueArray($value)
    {
        if (!is_array($value)) {
            return false;
        }

        return array_keys($value) === range(0, count($value) - 1);
    }

    protected function convertToObject($value)
    {
        if (is_array($value)) {
            $value = (object) $value;
        }

        return (is_object($value)) ? $value : new \stdClass();
    }

    protected function convertToArray($value)
    {
        return (is_array($value)) ? array_values($value) : [];
    }

    protected function convertToString($value)
    {
        return strval($value);
    }

    protected function convertToInteger($value)
    {
        return intval($value);
    }

    protected function convertToNumber($value)
    {
        return floatval($value);
    }

    protected function convertToBoolean($value)
    {
        return boolval($value);
    }

    protected function convertToNull($value)
    {
        return null;
    }


    protected function storeObject($value)
    {
        foreach ($value as $k => $v) {
            $this->offsetSet($k, $v);
        }
    }

    protected function storeArray($value)
    {
        // append
    }

    protected function storeString($value)
    {
        $this->scalarValue = $value;
    }

    protected function storeInteger($value)
    {
        $this->scalarValue = $value;
    }

    protected function storeNumber($value)
    {
        $this->scalarValue = $value;
    }

    protected function storeBoolean($value)
    {
        $this->scalarValue = $value;
    }

    protected function storeNull($value)
    {
        $this->scalarValue = $value;
    }

    protected function isAggregateType()
    {
        $type = $this->getPrimaryType();
        return ($type === 'object' || $type === 'array');
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
        // TODO: resolve this for scalar values. See toScalar
        return 'ArrayObject'; // Array | Object | {scalar value}
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
    final public function offsetGet($index)
    {
        // TODO: Verify location
        return parent::offsetGet($index);

    }


    /**
     * Fill the (empty) root object with data
     * @param $data
     */
    public function hydrate($data)
    {
        // TODO: enum
        $type = $this->getPrimaryType();
        if (!$this->{'is'.$type}($data)) {
            $data = $this->{'convertTo'.$type}($data);
        }
        // TODO: Validate data

        $this->{'store'.$type}($data);

    }


    protected function getPrimaryType()
    {
        return (is_array($this->type)) ? $this->type[0] : $this->type;
    }

    /**
     * Convert the specified arrayObj to a stdClass object.  Ultimately, this is a decorator around the toJson() method.
     * Note that empty arrays do not get represented properly -- the json_decode(json_encode()) trick returns an empty
     * array.
     * @param Dto $arrayObj
     *
     * @return object stdClass
     */
    public function toObject(Dto $arrayObj = null)
    {
        $result = $this->toJson(false, $arrayObj);

        // Handle case where empty arrays are not converted to objects
        if ($result === '[]') {
            $result = new \stdClass();
        }
        else {
            $result = json_decode($this->toJson(false, $arrayObj));
        }

        return $result;
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
     *
     * @return mixed
     * @throws \Exception
     */
    public function toScalar()
    {
        if ($this->isAggregateType()) {
            throw new \Exception('This DTO stores aggregate data and cannot be represented as a scalar value.');
        }

        return $this->scalarValue;
    }
    

}
