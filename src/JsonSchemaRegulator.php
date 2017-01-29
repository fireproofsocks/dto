<?php

namespace Dto;


class JsonSchemaRegulator implements RegulatorInterface
{
    protected $serviceContainer;

    protected $schemaAccessor;

    public function __construct(\ArrayAccess $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;

        // TODO DI
        $this->schemaAccessor = $serviceContainer[JsonSchemaAcessorInterface::class];
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
     * @param $input mixed
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

    public function getSchema()
    {
        // Dereference
    }

    /**
     * @inheritDoc
     */
    public function isObject()
    {
        // TODO: Implement isObject() method.
    }

    /**
     * @inheritDoc
     */
    public function isArray()
    {
        // TODO: Implement isArray() method.
    }

    /**
     * @inheritDoc
     */
    public function isScalar()
    {
        // TODO: Implement isScalar() method.
    }

    /**
     * Schema data can be loaded in different ways.
     *
     *  1. PHP Array is injected
     *  2. Name of JSON schema file is injected
     *
     * @param $schema mixed
     * @return array
     */
    public function setSchema($schema = null)
    {
        if (is_null($schema)) {
            $schema = include 'default_root_schema.php';
        }

        $schema = $this->serviceContainer[ResolverInterface::class]->resolveSchema($schema);
        $this->schemaAccessor->set($schema);
        return $schema;
    }


}