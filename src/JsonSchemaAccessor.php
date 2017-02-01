<?php

namespace Dto;

use Dto\Exceptions\DefinitionNotFoundException;
use Dto\Exceptions\InvalidKeyException;
use Dto\Exceptions\InvalidReferenceException;

class JsonSchemaAccessor implements JsonSchemaAccessorInterface
{
    protected $serviceContainer;

    protected $schema;

    public function __construct(\ArrayAccess $serviceContainer, $schema = null)
    {
        $this->serviceContainer = $serviceContainer;

        if (!is_null($schema)) {
            $this->set($schema);
        }
    }

    /**
     * Support fluid interface for greater clarity
     * @param array $schema
     * @return $this
     */
    public function load(array $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    public function getAllOf()
    {
        return isset($this->schema['allOf']) ? $this->schema['allOf'] : [];
    }

    public function getAnyOf()
    {
        // TODO: This keyword's value MUST be an array. This array MUST have at least one element.
        return isset($this->schema['anyOf']) ? $this->schema['anyOf'] : false;
    }

    public function getAdditionalItems()
    {
        // If this keyword is absent, it may be considered present with an empty schema.
        return isset($this->schema['additionalItems']) ? $this->schema['additionalItems'] : [];
    }

    public function getItems()
    {
        // The value of "items" MUST be either a schema or array of schemas.
        return (isset($this->schema['items'])) ? $this->schema['items'] : [];
    }

    public function getMaxLength()
    {
        // TODO: The value of this keyword MUST be a non-negative integer.
        return (isset($this->schema['maxLength'])) ? $this->schema['maxLength'] : false;
    }

    public function getMinLength()
    {
        // TODO: The value of this keyword MUST be an integer. This integer MUST be greater than, or equal to, 0.
        return (isset($this->schema['minLength'])) ? $this->schema['minLength'] : false;
    }

    public function getMaxProperties()
    {
        // TODO: The value of this keyword MUST be an integer. This integer MUST be greater than, or equal to, 0.
        return (isset($this->schema['maxProperties'])) ? $this->schema['maxProperties'] : false;
    }

    public function getMinProperties()
    {
        // TODO: The value of this keyword MUST be an integer. This integer MUST be greater than, or equal to, 0.
        return (isset($this->schema['minProperties'])) ? $this->schema['minProperties'] : false;
    }

    public function getMaxItems()
    {
        // TODO: The value of this keyword MUST be an integer. This integer MUST be greater than, or equal to, 0.
        return (isset($this->schema['maxItems'])) ? $this->schema['maxItems'] : false;
    }

    public function getMinItems()
    {
        // TODO: The value of this keyword MUST be an integer. This integer MUST be greater than, or equal to, 0.
        return (isset($this->schema['minItems'])) ? $this->schema['minItems'] : false;
    }

    public function getUniqueItems()
    {
        // TODO: The value of this keyword MUST be a boolean.
        return (bool) (isset($this->schema['uniqueItems'])) ? $this->schema['uniqueItems'] : false;
    }

    public function getPattern()
    {
        return (isset($this->schema['pattern'])) ? $this->schema['pattern'] : false;
    }

    public function getProperties()
    {
        return (isset($this->schema['properties'])) ? $this->schema['properties'] : [];
    }

    public function getMultipleOf()
    {
        // TODO: The value of "multipleOf" MUST be a number, strictly greater than 0.
        return (isset($this->schema['multipleOf'])) ? $this->schema['multipleOf'] : false;
    }

    public function getMaximum()
    {
        return (isset($this->schema['maximum'])) ? $this->schema['maximum'] : false;
    }

    public function getExclusiveMaximum()
    {
        return (isset($this->schema['exclusiveMaximum'])) ? $this->schema['exclusiveMaximum'] : false;
    }

    public function getMinimum()
    {
        return (isset($this->schema['minimum'])) ? $this->schema['minimum'] : false;
    }

    public function getExclusiveMinimum()
    {
        return (isset($this->schema['exclusiveMinimum'])) ? $this->schema['exclusiveMinimum'] : false;
    }

    public function getAdditionalProperties()
    {
        return (array_key_exists('additionalProperties', $this->schema)) ? $this->schema['additionalProperties'] : [];
    }

    public function getPatternProperties()
    {
        return (array_key_exists('patternProperties', $this->schema)) ? $this->schema['patternProperties'] : [];
    }

    /**
     * @return mixed | string | array | boolean
     */
    public function getType()
    {
        return (isset($this->schema['type'])) ? $this->schema['type'] : false;
    }

    public function getDefault()
    {
        return (isset($this->schema['default'])) ? $this->schema['default'] : null;
    }

    public function getEnum()
    {
        // TODO: must be an array
        return (isset($this->schema['enum'])) ? $this->schema['enum'] : false;
    }

    public function getRef()
    {
        $ref = (isset($this->schema['$ref'])) ? $this->schema['$ref'] : false;

        if ($ref !== false && !is_string($ref)) {
            throw new InvalidReferenceException('"$ref" must contain a string.');
        }

        return $ref;
//        if (!isset($this->schema['$ref'])) {
//            return false;
//        }
//        if (!is_string($this->schema['$ref'])) {
//            throw new InvalidReferenceException('The "$ref" parameter must store a string.');
//        }
//        // Get local definition
//        if ('#' === substr($this->schema['$ref'], 0, 1)) {
//            return $this->getLocalReference($this->schema['ref']);
//        }
//        elseif (class_exists($this->schema['$ref'])) {
//            return $this->getPhpReference($this->schema['ref']);
//        }
//
//        return $this->getRemoteReference($this->schema['ref']);
//        // is PHP classname?
//        // is JSON file?
//        //$this->schema['$ref'];
    }

    public function getRequired()
    {
        return (isset($this->schema['required'])) ? $this->schema['required'] : [];
    }

    public function getDescription()
    {
        return (isset($this->schema['description'])) ? $this->schema['description'] : '';
    }

    public function get($key)
    {
        if (!is_string($key)) {
            throw new InvalidKeyException('get() requires a string key.');
        }
        if (!array_key_exists($key, $this->schema)) {
            throw new InvalidKeyException('The key "'.$key.'" does not exist in this schema.');
        }
    }

    public function getDefinition($name)
    {
        if (isset($this->schema['definitions'][$name])) {
            return $this->schema['definitions'][$name];
        }

        throw new DefinitionNotFoundException('"'.$name.'" not found in schema definitions.');
    }

    public function getSchema()
    {
        return $this->schema;
    }
}