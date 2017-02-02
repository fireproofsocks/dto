<?php

namespace Dto;


use Dto\Exceptions\InvalidKeyException;

class JsonSchemaRegulator implements RegulatorInterface
{
    protected $serviceContainer;

    /**
     * @var array
     */
    protected $schema = [];

    protected $schemaAccessor;

    protected $isObject;

    protected $isArray;

    protected $isScalar;


    public function __construct(\ArrayAccess $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;

        // TODO DI
        $this->schemaAccessor = $serviceContainer[JsonSchemaAccessorInterface::class];
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
        $validators = $this->serviceContainer[ValidatorSelectorInterface::class]->selectValidators($schema);

        // can we do any filtering?

        // throws Exceptions on errors
        foreach ($validators as $v) {
            $result = $v->validate($value);
            // TODO: feels smelly
            if ($v->isFilteredValue()) {
                $value = $v->getFilteredValue();
            }
        }


        // filter -- is any filtering required before storage?

        // TODO: set storage type: isObject, isArray, isScalar

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
     * @param $index
     * @return array
     */
    public function getSchemaAtIndex($index)
    {
        return [];
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
        // This is only known after filter() has completed
        // TODO: Implement isObject() method.
        return $this->isObject;
    }

    /**
     * @inheritDoc
     */
    public function isArray()
    {
        // This is only known after filter() has completed
        // TODO: Implement isArray() method.
        return $this->isArray;
    }

    /**
     * @inheritDoc
     */
    public function isScalar()
    {
        // This is only known after filter() has completed
        // TODO: Implement isScalar() method.
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
        $this->schema = $this->serviceContainer[ResolverInterface::class]->resolveSchema($schema);
        return $this->schema;
        //$this->schemaAccessor->load($this->schema);
        //return $this->schema;
    }


}