<?php

namespace Dto;

class JsonSchemaAccessor implements JsonSchemaAcessorInterface
{
    protected $schema;

    public function set(array $schema)
    {
        $this->schema = $schema;
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
     * @return mixed | string | array
     */
    public function getType()
    {
        return (isset($this->schema['type'])) ? $this->schema['type'] : '';
    }

    public function getDefault()
    {
        return (isset($this->schema['default'])) ? $this->schema['default'] : null;
    }

    public function getEnum()
    {
        return (isset($this->schema['enum'])) ? $this->schema['enum'] : false;
    }
}