<?php

namespace Dto;


class JsonSchemaRegulator implements RegulatorInterface
{
    protected $serviceContainer;

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
    public function filter($value)
    {
        // TODO: Implement validate() method.
        // de-reference root schema
        // detect primary validator (enum, oneOf, allOf, type
        // validate
        // filter

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
        $default = $this->schemaAccessor->getDefault();

        if ($input instanceof DtoInterface) {
            $input = ($input->isScalar()) ? $input->toScalar() : $input->toArray();
        }
        elseif (is_object($input)) {
            $input = (array) $input;
        }

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
     */
    public function getSchemaAtKey($key)
    {
        return [];
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
        $schema = $this->serviceContainer[ResolverInterface::class]->resolveSchema($schema);
        $this->schemaAccessor->set($schema);
        return $schema;
    }


}