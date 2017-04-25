<?php

namespace Dto;

use Dto\Exceptions\DefinitionNotFoundException;
use Dto\Exceptions\InvalidReferenceException;

class JsonSchemaAccessor implements JsonSchemaAccessorInterface
{
    protected $schema = [];

    /**
     *
     * @param array $schema
     * @return $this
     */
    public function factory(array $schema)
    {
        $this->schema = $schema;
        return $this;
    }

    public function getId()
    {
        // TODO: MUST represent a valid URI-reference [RFC3986]
        // SHOULD NOT be an empty fragment <#> or an empty string <>
        return isset($this->schema['id']) ? $this->schema['id'] : '';
    }

    public function getAllOf()
    {
        return isset($this->schema['allOf']) ? $this->schema['allOf'] : false;
    }

    public function getAnyOf()
    {
        // TODO: This keyword's value MUST be an array. This array MUST have at least one element.
        return isset($this->schema['anyOf']) ? $this->schema['anyOf'] : false;
    }

    public function getOneOf()
    {
        return isset($this->schema['oneOf']) ? $this->schema['oneOf'] : false;
    }

    public function getNot()
    {
        return isset($this->schema['not']) ? $this->schema['not'] : false;
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
        // TODO: the value must be an associative array
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

    /**
     * @link http://json-schema.org/example2.html
     * @return array
     */
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

    public function getFormat()
    {
        return (isset($this->schema['format'])) ? $this->schema['format'] : false;
    }

    public function getRef()
    {
        $ref = (isset($this->schema['$ref'])) ? $this->schema['$ref'] : false;

        if ($ref !== false && !is_string($ref)) {
            throw new InvalidReferenceException('"$ref" must contain a string.');
        }

        return $ref;
    }

    public function getRequired()
    {
        return (isset($this->schema['required'])) ? $this->schema['required'] : [];
    }

    public function getTitle()
    {
        return (isset($this->schema['title'])) ? $this->schema['title'] : '';
    }

    public function getDescription()
    {
        return (isset($this->schema['description'])) ? $this->schema['description'] : '';
    }

    public function getDefinition($name)
    {
        if (isset($this->schema['definitions'][$name])) {
            return $this->mergeMetaData($this->schema['definitions'][$name]);
        }

        throw new DefinitionNotFoundException('"'.$name.'" not found in schema definitions.');
    }

    public function getDefinitions()
    {
        return (isset($this->schema['definitions'])) ? $this->schema['definitions'] : [];
    }

    public function getSchema()
    {
        return (isset($this->schema['$schema'])) ? $this->schema['$schema'] : '';
    }

    public function setId($id)
    {
        $this->schema['id'] = $id;
    }

    public function setSchema($schema)
    {
        $this->schema['$schema'] = $schema;
    }

    public function setTitle($title)
    {
        $this->schema['title'] = $title;
    }

    public function setDescription($description)
    {
        $this->schema['description'] = $description;
    }

    public function setDefinitions(array $definitions)
    {
        $this->schema['definitions'] = $definitions;
    }


    public function toArray()
    {
        return $this->schema;
    }


    public function mergeMetaData(array $child_schema)
    {
        $child = (new JsonSchemaAccessor())->factory($child_schema);

        if (!$child->getId()) {
            if ($this->getId()) {
                $child->setId($this->getId());
            }
            if ($this->getSchema()) {
                $child->setSchema($this->getSchema());
            }
            if ($this->getDefinitions()) {
                $child->setDefinitions($this->getDefinitions());
            }
        }

        if (!$child->getTitle()) {
            if ($this->getTitle()) {
                $child->setTitle($this->getTitle());
            }
        }

        if (!$child->getDescription()) {
            if ($this->getDescription()) {
                $child->setDescription($this->getDescription());
            }
        }

        return $child->toArray();
    }
}