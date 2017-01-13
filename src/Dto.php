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
     * Optional in-class schema definition
     *
     * @var mixed
     */
    protected $schema;

    /**
     * Optional default values
     * @var
     */
    protected $default;


    /**
     * @var Detector
     */
    protected $detector;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var JsonSchema
     */
    protected $jsonSchema;

    /**
     * Only relevant when type is not an aggregate (i.e. when type is neither object nor array)
     * @var
     */
    protected $scalarStorage;

    /**
     * Dto constructor.
     *
     * @param mixed $input values filtered against the $template and $meta
     * @param $jsonSchema JsonSchema|null
     */
    public function __construct($input = null, JsonSchema $jsonSchema = null)
    {
        $this->setFlags(0);

        $this->jsonSchema = ($jsonSchema) ? $jsonSchema : new JsonSchema($this->schema);

        // Filter input
        if ($input === null) {
            parent::__construct();
        }
        else {
            parent::__construct($input);
        }
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
        if ($bypass || $this->jsonSchema->isPropertySettable($index)) {
            parent::offsetSet($index, $newval);
        }

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
        $this->scalarStorage = $value;
    }

    protected function storeInteger($value)
    {
        $this->scalarStorage = $value;
    }

    protected function storeNumber($value)
    {
        $this->scalarStorage = $value;
    }

    protected function storeBoolean($value)
    {
        $this->scalarStorage = $value;
    }

    protected function storeNull($value)
    {
        $this->scalarStorage = $value;
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

        return $this->scalarStorage;
    }
    

}
