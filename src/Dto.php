<?php

namespace Dto;

use Dto\Exceptions\InvalidArrayOperationException;
use Dto\Exceptions\InvalidDataTypeException;
use Dto\Exceptions\InvalidIndexException;
use Dto\Exceptions\InvalidKeyException;
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


    /** @var RegulatorInterface */
    protected $regulator;

    /**
     * Tracks which index of the array we are writing to
     * @var integer
     */
    protected $items_cnt = 0;

    /** @var string */
    protected $storage_type;

    /** @var  string : directory used as base for relative $ref's (no trailing slash) */
    protected $baseDir = __DIR__;

    /**
     * Dto constructor.
     *
     * @param mixed $input value
     * @param mixed $schema
     * @param mixed $regulator
     */
    public function __construct($input = null, $schema = null, RegulatorInterface $regulator = null)
    {
        $this->setFlags(0);

        $this->regulator = $this->getDefaultRegulator($regulator);

        // Resolve Schema references
        $this->schema = $this->regulator->compileSchema((is_null($schema)) ? $this->schema : $schema, $this->getBaseDir());

        $this->hydrate($input);
    }

    /**
     * @param mixed $regulator
     * @return RegulatorInterface
     */
    protected function getDefaultRegulator($regulator)
    {
        if (is_null($regulator)) {
           return new JsonSchemaRegulator(new ServiceContainer(), get_called_class());
        }

        return $regulator;
    }

    /**
     * Used for object notation, e.g. print $dto->foo
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
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
     * This deals ONLY with objects (not arrays).
     * @param $key mixed
     * @param $value mixed
     * @return void
     * @throws InvalidDataTypeException
     */
    public function set($key, $value)
    {
        if ($this->storage_type !== 'object') {
            throw new InvalidDataTypeException('Properties can only be set on objects.');
        }

        if (parent::offsetExists($key)) {
            parent::offsetGet($key)->hydrate($value);
        }
        else {
            parent::offsetSet($key, $this->regulator->getFilteredValueForKey($value, $key, $this->schema));
            $this->regulator->postValidate($this);
        }
    }
    
    /**
     * Called by array access.
     *
     * @param mixed $index
     * @param mixed $value
     *
     * @throws InvalidArrayOperationException
     * @throws InvalidIndexException
     */
    final public function offsetSet($index, $value)
    {
        // Integers + null only?
        if ($this->storage_type !== 'array') {
            throw new InvalidArrayOperationException('This operation is reserved for arrays only.');
        }

        // Does the property name match the regex? etc.
        if (is_null($index)) {
            parent::offsetSet(null, $this->regulator->getFilteredValueForIndex($value, $this->items_cnt, $this->schema));
            $this->regulator->postValidate($this);
            $this->items_cnt = $this->items_cnt + 1;
            return;
        }
        elseif (parent::offsetExists($index)) {
            parent::offsetGet($index)->hydrate($value);
            return;
        }

        throw new InvalidIndexException('Index "'.$index.'" not found in array.');

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
     * @return string
     */
    public function __toString()
    {
        return strval($this->toScalar());
    }
    
    /**
     * Append a value to the end of an array.  Defers to offsetSet to determine if location is valid for appending.
     * @link http://php.net/manual/en/arrayobject.append.php.
     * @throws InvalidDataTypeException
     * @param mixed $val
     */
    public function append($val)
    {
        if (!$this->regulator->isArray()) {
            throw new InvalidDataTypeException('Array operations are not allowed by the current schema.');
        }

        $this->offsetSet(null, $val);
    }


    public function get($key)
    {
        if (!is_scalar($key)) {
            throw new \InvalidArgumentException('Invalid data type for get() method. Scalar required.');
        }

        if ($this->storage_type === 'scalar') {
            throw new InvalidDataTypeException('The get() method cannot be used on scalar objects.  Use toScalar() instead.');
        }

        if (parent::offsetExists($key)) {
            return parent::offsetGet($key);
        }

        // TODO: dynamically deepen object structure?
        throw new InvalidKeyException('The key "'.$key.'" does not exist in this DTO.');

    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function forget($index)
    {
        return $this->offsetUnset($index);
    }

    final public function offsetUnset($index)
    {
        parent::offsetUnset($index);

        $this->hydrate($this->toArray()); // reindex, re-validate
    }

    public function exists($index) {
        return $this->offsetExists($index);
    }

    final public function offsetExists($index)
    {
        return parent::offsetExists($index);
    }

    /**
     * Used when accessing the instance via array notation.
     * @param mixed $index
     * @return mixed
     *
     * @throws InvalidIndexException
     */
    final public function offsetGet($index)
    {
        if (parent::offsetExists($index)) {
            return parent::offsetGet($index);
        }

        throw new InvalidIndexException('Index "'.$index.'" not found in array.');
    }


    /**
     * Fill the current object with data
     * @param $value mixed
     * @throws UnstorableValueException
     */
    public function hydrate($value)
    {

        $value = $this->regulator->getDefault($value, $this->schema);

        $value = $this->regulator->rootFilter($value, $this->schema);

        $this->storage_type = $this->regulator->chooseDataStorageType($value, $this->schema);

        if ($this->storage_type === 'object') {
            $this->hydrateObject($value);
        }
        elseif ($this->storage_type === 'array') {
            $this->hydrateArray($value);
        }
        else {
            $this->hydrateScalar($value);
        }
    }

    protected function hydrateObject($value)
    {
        foreach ($value as $k => $v) {
            $value[$k] = $this->regulator->getFilteredValueForKey($v, $k, $this->schema);
        }

        parent::exchangeArray($value);
    }

    protected function hydrateArray($array)
    {
        foreach ($array as $index => $v) {
            $array[$index] = $this->regulator->getFilteredValueForIndex($v, $index, $this->schema);
        }

        $this->items_cnt = count($array);
        parent::exchangeArray($array);
    }

    /**
     * Scalar values are stored in the zeroth place of the ArrayObject
     * @param $value
     */
    protected function hydrateScalar($value)
    {
        parent::offsetSet(0, $value);
    }

    /**
     * Returns a (deeply nested) stdClass representation of the data.
     *
     * @return \stdClass
     * @throws InvalidDataTypeException
     */
    public function toObject()
    {
        if ($this->storage_type === 'scalar') {
            throw new InvalidDataTypeException('Object representation is not possible for scalar values.');
        }

        $output = new \stdClass();
        foreach ($this as $k => $v) {
            $output->{$k} = ($v->getStorageType() === 'scalar') ? $v->toScalar() : $v->toObject();
        }

        return $output;
    }

    /**
     * @return string
     */
    final public function serialize() {
        return $this->toJson();
    }

    /**
     * Convert the specified arrayObj to JSON.  Ultimately, this is a decorator around the toArray() method.
     *
     * @param bool $pretty
     *
     * @return string
     */
    public function toJson($pretty = false)
    {
        // JSON can represent scalars!
        if ($this->storage_type === 'scalar') {
            return json_encode(parent::offsetGet(0), JSON_PRETTY_PRINT);
        }

        $data = $this->toArray();

        // Disambiguate between empty arrays [] and empty objects {}
        if (empty($data)) {
            if ($this->regulator->isArray()) {
                return '[]';
            }
            return '{}';
        }

        return ($pretty) ? json_encode($data, JSON_PRETTY_PRINT) : json_encode($data);
    }


    /**
     * returning $this type-cast to an array is too simplistic, unfortunately
     * @return array
     * @throws InvalidDataTypeException
     */
    public function toArray()
    {
        if ($this->storage_type === 'scalar') {
            throw new InvalidDataTypeException('Array representation is not possible for scalar values.');
        }

        $output = [];
        foreach ($this as $k => $v) {
            $output[$k] = ($v->getStorageType() === 'scalar') ? $v->toScalar() : $v->toArray();
        }

        return $output;
    }

    /**
     * Scalar values are stored in the zeroth place of the ArrayObject
     * @return mixed
     * @throws \Exception
     */
    public function toScalar()
    {
        if ($this->storage_type !== 'scalar') {
            throw new InvalidDataTypeException('This DTO stores aggregate data and cannot be represented as a scalar value.');
        }

        return parent::offsetGet(0);
    }

    public function getStorageType()
    {
        return $this->storage_type;
    }

    public function getBaseDir()
    {
        return $this->baseDir;
    }
}
