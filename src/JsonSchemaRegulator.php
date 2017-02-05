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
     * What is this function doing really?  It's doing high-level validation and filtering to determine a storage type.
     * @inheritDoc
     */
    public function preFilter($value, array $schema = [])
    {

        // TODO: Implement validate() method.
        // de-reference root schema (done already in compileSchema)

        $value = $this->unwrapValue($value);

        // detect primary validator (enum, oneOf, allOf, type
        $validators = $this->container[ValidatorSelectorInterface::class]->selectValidators($schema);

        // print count($validators);

        // can we do any filtering?

        // throws Exceptions on errors
        foreach ($validators as $v) {
            //print get_class($v); exit;
            $value = $v->validate($value, $schema);
        }


        // filter -- is any filtering required before storage?

        // TODO: set storage type: isObject, isArray, isScalar
//        patternProperties
//        $this->setStorageType($value, $schema);
        //var_dump($this->setStorageType($value, $schema)); exit;

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

    public function chooseDataStorageType($value, array $schema)
    {

        // Polymorphism: all options are possible until we collapse them...
        $this->isObject = true;
        $this->isScalar = true;
        $this->isArray = true;

        $this->schemaAccessor->load($schema);
        $type = $this->schemaAccessor->getType();

        if ($type && !is_array($type)) {
            if ($type === 'object') {
                $this->isScalar = false;
                $this->isArray = false;
                return 'object';
            }
            elseif ($type == 'array') {
                $this->isScalar = false;
                $this->isObject = false;
                return 'array';
            }
            else {
                $this->isObject = false;
                $this->isArray = false;
                return 'scalar';
            }
        }

        // Empty arrays are the rub: they are considered arrays by DTO
        if ($this->container[TypeDetectorInterface::class]->isArray($value)) {
            $this->isArray = true;
            return 'array';
        }
        elseif ($this->container[TypeDetectorInterface::class]->isObject($value)) {
            $this->isObject = true;
            return 'object';
        }
        else {
            $this->isScalar = true;
            return 'scalar';
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
     * @pararm $schema array
     * @return array
     * @throws InvalidIndexException
     */
    protected function getSchemaAtIndex($index, $schema)
    {
        $accessor = $this->schemaAccessor->load($schema);

        if ($maxItems = $this->schemaAccessor->getMaxItems()) {
            if (($index + 1) > $maxItems) {
                throw new InvalidIndexException('Arrays with more than '.$maxItems.' items disallowed by "maxItems".');
            }
        }

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
     * @param $schema array
     * @return array
     * @throws InvalidKeyException
     */
    protected function getSchemaAtKey($key, $schema)
    {
        $accessor = $this->schemaAccessor->load($schema);

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
    }

    public function getFilteredValueForIndex($value, $index, array $schema)
    {
        return new Dto($value, $this->getSchemaAtIndex($index, $schema), $this);
    }

    public function getFilteredValueForKey($value, $key, array $schema)
    {
        return new Dto($value, $this->getSchemaAtKey($key, $schema), $this);
    }

    public function filterArray($value, $schema)
    {
        return $this->container['arrayValidator']->validate($value, $schema);
    }

    public function filterObject($value, $schema)
    {
        return $this->container['objectValidator']->validate($value, $schema);
    }
}