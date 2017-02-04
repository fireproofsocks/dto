<?php

namespace Dto;


use Dto\Exceptions\InvalidIndexException;
use Dto\Exceptions\InvalidIntegerValueException;
use Dto\Exceptions\InvalidKeyException;

class JsonSchemaRegulator implements RegulatorInterface
{
    protected $container;

    /**
     * @var array
     */
    protected $schema = [];

    protected $schemaAccessor;

    protected $isObject;

    protected $isArray;

    protected $isScalar;


    public function __construct(\ArrayAccess $container)
    {
        $this->container = $container;

        // TODO DI
        $this->schemaAccessor = $container[JsonSchemaAccessorInterface::class];
    }

    /**
     * @inheritDoc
     */
    public function filter($value, array $schema = [])
    {
        // TODO: Implement validate() method.
        // de-reference root schema (done already in compileSchema)

        $value = $this->unwrapValue($value);

        // detect primary validator (enum, oneOf, allOf, type
        $validators = $this->container[ValidatorSelectorInterface::class]->selectValidators($schema);

        // can we do any filtering?

        // throws Exceptions on errors
        foreach ($validators as $v) {
            $result = $v->validate($value, $schema);
            // TODO: feels smelly
            if ($v->isFilteredValue()) {
                $value = $v->getFilteredValue();
            }
        }


        // filter -- is any filtering required before storage?

        // TODO: set storage type: isObject, isArray, isScalar
        $this->setStorageType($value);

        return $value;
    }

    /**
     * Normalize the internal data type: convert DTOs to scalars/arrays, PHP stdClass objects to PHP associative arrays
     * @param $value
     * @return array
     */
    protected function unwrapValue($value)
    {
        if ($value instanceof DtoInterface) {
            $value = ($value->isScalar()) ? $value->toScalar() : $value->toArray();
        }
        elseif (is_object($value)) {
            $value = (array) $value;
        }

        return $value;
    }

    protected function setStorageType($value)
    {
        $this->isObject = false;
        $this->isScalar = false;
        $this->isArray = false;

        $type = $this->schemaAccessor->getType();

        if (!is_array($type)) {
            if ($type === 'object') {
                $this->isObject = true;
            }
            elseif ($type == 'array') {
                $this->isArray = true;
            }
            else {
                $this->isScalar = true;
            }
            return;
        }

        if ($this->container[TypeDetectorInterface::class]->isObject($value)) {
            $this->isObject = true;
        }
        elseif ($this->container[TypeDetectorInterface::class]->isArray($value)) {
            $this->isArray = true;
        }
        else {
            $this->isScalar = true;
        }
    }

    /**
     * if input is null, use default
     * if input is scalar and default is scalar, use input
     * if input is array and default is array, merge arrays
     * @param $input mixed passed from DTO constructor
     * @return mixed|null
     */
    public function getDefault($input = null)
    {
        $default = $this->schemaAccessor->load($this->schema)->getDefault();

        $input = $this->unwrapValue($input);

        if (is_null($input)) {
            return $default;
        }
        elseif (is_null($default)) {
            return $input;
        }
        elseif (is_scalar($input) && is_scalar($default)) {
            return $input;
        }
        elseif (is_array($input) && is_array($default)) {
            return array_merge($default, $input);
        }

        throw new \InvalidArgumentException('Input data type conflicts with data type of schema default.');
    }

    /**
     * For getting the sub-schema corresponding to an array index
     * @link https://spacetelescope.github.io/understanding-json-schema/reference/array.html
     * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.9
     * @param $index
     * @return array
     * @throws InvalidIndexException
     */
    public function getSchemaAtIndex($index)
    {
        $accessor = $this->schemaAccessor->load($this->schema);

        $items = $accessor->getItems();

        // Is it a regular schema?  Each item must validate against this schema.
        if ($this->container[TypeDetectorInterface::class]->isObject($items)) {
            return $items;
        }

        // Is a tuple (an array of schemas)?
        if ($this->container[TypeDetectorInterface::class]->isArray($items)) {
            if (isset($items[$index])) {
                return $items[$index];
            }
        }

        // We have exceeded the number of schemas defining the tuple...
        $additionalItems = $accessor->getAdditionalItems();
        if ($this->container[TypeDetectorInterface::class]->isBoolean($additionalItems)) {
            return [];
        }
        if ($this->container[TypeDetectorInterface::class]->isObject($additionalItems)) {
            return $additionalItems;
        }

        throw new InvalidIndexException('Index not allowed by "items" and/or "additionalItems": '.$index);
    }

    /**
     * For getting the sub-schema corresponding to an object key
     * @param $key string
     * @return array
     * @throws InvalidKeyException
     */
    public function getSchemaAtKey($key)
    {
        $accessor = $this->schemaAccessor->load($this->schema);

        $properties = $accessor->getProperties();

        if (array_key_exists($key, $properties)) {
            return $properties[$key];
        }

        if($patternProperties = $accessor->getPatternProperties()) {
            foreach ($patternProperties as $regex => $schema) {
                if (preg_match('/'.$regex.'/', $key)) {
                    return $schema;
                }
            }
        }

        $additionalProperties = $accessor->getAdditionalProperties();

        if ($additionalProperties === true) {
            return []; // empty schema: anything goes
        }
        elseif (is_array($additionalProperties)) {
            return $additionalProperties; // as a schema
        }

        throw new InvalidKeyException('Key not allowed by "properties", "patternProperties", or "additionalProperties": '.$key);
    }

    /**
     * @inheritDoc
     */
    public function isObject()
    {
        return $this->isObject;
    }

    /**
     * @inheritDoc
     */
    public function isArray()
    {
        return $this->isArray;
    }

    /**
     * @inheritDoc
     */
    public function isScalar()
    {
        return $this->isScalar;
    }

    /**
     * Let the resolver class handle how the Schema is resolved.  This
     * is set when the DTO is instantiated because the DTO contains the schema.
     *
     * @param $schema mixed
     * @return array
     */
    public function compileSchema($schema = null)
    {
        $this->schema = $this->container[ResolverInterface::class]->resolveSchema($schema);
        return $this->schema;
        //$this->schemaAccessor->load($this->schema);
        //return $this->schema;
    }


}