<?php

namespace Dto;

use Dto\Exceptions\AppendException;
use Dto\Exceptions\InvalidDataTypeException;
use Dto\Exceptions\InvalidPropertyException;
use Dto\Exceptions\UnstorableValueException;

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
     * @var RegulatorInterface
     */
    protected $regulator;

    /**
     * @var ConfigInterface
     */
    protected $config;


    /**
     * @var TypeConverterInterface
     */
    protected $converter;

    /**
     * Dto constructor.
     *
     * @param mixed $input value
     * @param $regulator RegulatorInterface|null
     * @param $config ConfigInterface|null
     */
    public function __construct($input = null, RegulatorInterface $regulator = null, ConfigInterface $config = null)
    {
        $this->setFlags(0);

        $this->regulator = ($regulator) ? $regulator : new JsonSchema($this->schema);

        $this->config = ($config) ? $config : new Config();

        $this->converter = new TypeConverter();
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
        return $this->offsetGet($name);
    }


    /**
     * Accessed when the object is written to via object notation.
     *
     * @param $name
     * @param $value
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
     * @throws InvalidPropertyException
     */
    final public function offsetSet($index, $newval, $bypass = false)
    {
        if ($bypass) {
            parent::offsetSet($index, $newval);
        }
        // Does the property name match the regex? etc.
        $schema = $this->regulator->getSchema($index);

        // TODO: convert value
        // TODO: validate value
        // if is object or array?  Loop over keys/values???
        parent::offsetSet($index, $this->getHydratedChildDto($newval, $schema));

    }

    /**
     * @param $index string attribute name
     *
     * @return bool
     */
    public function __isset($index)
    {
        return $this->offsetExists($index);
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
     * @link http://php.net/manual/en/arrayobject.append.php.
     * @throws InvalidDataTypeException
     * @param mixed $val
     */
    public function append($val)
    {
        if ($this->regulator->isArray()) {
            throw new InvalidDataTypeException('Array operations are not allowed by the current schema.');
        }

        // validate value

        $this->offsetSet(null, $val);
    }

    /**
     * @link https://stackoverflow.com/questions/6875080/php-how-to-array-unshift-on-an-arrayobject
     * @param $val
     * @throws InvalidDataTypeException
     */
    public function prepend($val)
    {
        if ($this->regulator->isArray()) {
            throw new InvalidDataTypeException('Array operations are not allowed by the current schema.');
        }
        // TODO
    }

    /**
     * @link https://stackoverflow.com/questions/6627266/array-slice-or-other-array-functions-on-arrayobject
     * @param $offset
     * @param null $length
     * @throws InvalidDataTypeException
     */
    public function slice($offset, $length = null)
    {
        if ($this->regulator->isArray()) {
            throw new InvalidDataTypeException('Array operations are not allowed by the current schema.');
        }
        // TODO
    }

    public function get($index)
    {
        return $this->offsetGet($index);
    }

    public function offsetUnset($index)
    {
        parent::offsetUnset($index); // TODO: Change the autogenerated stub
    }

    public function offsetExists($index)
    {
        parent::offsetExists($index); // TODO: Change the autogenerated stub
    }

    /**
     * The not-so-obvious role of this function is to trigger the dynamic deepening of the object structure.
     * @param mixed $index
     * @return mixed
     *
     * @throws InvalidPropertyException
     */
    final public function offsetGet($index)
    {
        // Already has property
        // this might get weird for "dual" types, e.g. we set it to a string, then try to use it as an object.
        //if (array_key_exists($index, $this)) {
        if (parent::offsetExists($index)) {
            return parent::offsetGet($index);
        }

        // We only want to deepen the structure if the data type is an object
        $schema = $this->regulator->getSchema($index);

        $this->deepenStructure($index, $schema);
        return parent::offsetGet($index);
    }

    protected function deepenStructure($index, $schema)
    {
        $child = $this->getHydratedChildDto($index, $schema);
        $this->offsetSet($index, $child);
    }

    protected function getHydratedChildDto($input = null, $schema = []) {
        // TODO: can we pass a reference to THIS object instead of creating a new instance?
        $className = get_called_class();
        return new $className($input, $schema, $this->config);
    }


    /**
     * Fill the (empty) root object with data
     * @param $value mixed
     * @throws UnstorableValueException
     */
    public function hydrate($value)
    {
        if (empty($this->regulator->getType())) {
            return $this->forceHydrate($value);
        }

        if ($type = $this->regulator->getStorableTypeByValue($value)) {

             $value = $this->converter->{'to' . $type}($value); // perform TypeConversion

             if ('object' === $type) {
                 $this->hydrateObject($value);
             }
             elseif ('array' === $type) {
                 $this->hydrateArray($value);
             }
             else {
                 $this->hydrateScalar($value);
             }
        }

         throw new UnstorableValueException('Value type not allowed by current schema.');
    }

    protected function hydrateObject($value)
    {
        // TODO? these may require "post" validation
//        if (!$this->regulator->isValidObject($value)) {
//            $this->config->doInvalidValue();
//        }

        parent::exchangeArray([]);

        foreach ($value as $k => $v) {
            $this->offsetSet($k, $v);
        }
    }

    protected function hydrateArray($value)
    {
        // TODO? these may require "post" validation
//        if (!$this->regulator->isValidArray($value)) {
//
//        }
        // clear the array,
        parent::exchangeArray([]);
        // append to it
        foreach ($value as $v) {
            // TODO: $v is valid?
            // TODO: is full?  Behavior if full (shift or ignore?)
            $this->offsetSet(null, $v);
        }
    }

    /**
     * Scalar values are stored in the zeroth place of the ArrayObject
     * @param $value
     */
    protected function hydrateScalar($value)
    {
        $this->regulator->checkValidScalar($value);

        parent::offsetSet(0, $value);
    }

    protected function forceHydrate($value)
    {
        $value = (is_scalar($value) || is_null($value)) ? [$value] : $value;
        parent::exchangeArray($value);
        return;
    }

    public function toObject()
    {

    }
    
    /**
     * Convert the specified arrayObj to JSON.  Ultimately, this is a decorator around the toArray() method.
     * TODO: consider overriding the serialize() method
     * @param bool $pretty
     *
     * @return string
     */
    public function toJson($pretty = false)
    {
        // JSON can represent scalars!
        // json_encode($this->toArray($arrayObj), JSON_PRETTY_PRINT);
    }
    

    public function toArray()
    {
        if ($this->regulator->isScalar()) {
            throw new InvalidDataTypeException('Array operations are not allowed by the current schema.');
        }
    }

    /**
     * Scalar values are stored in the zeroth place of the ArrayObject
     * @return mixed
     * @throws \Exception
     */
    public function toScalar()
    {
        if ($this->regulator->isScalar()) {
            throw new InvalidDataTypeException('This DTO stores aggregate data and cannot be represented as a scalar value.');
        }

        return parent::offsetGet(0);
    }
    

}
