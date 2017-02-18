<?php

namespace Dto;

/**
 * Interface JsonSchemaAccessorInterface
 *
 * For interacting with a JSON Schema object, useful because some schema attributes infer different default values.
 *
 * @package Dto
 */
interface JsonSchemaAccessorInterface
{
    public function factory(array $schema);

    public function getId();

    public function getAllOf();

    public function getAnyOf();

    public function getOneOf();

    public function getNot();

    public function getAdditionalItems();

    public function getItems();

    public function getMaxLength();

    public function getMinLength();

    public function getMaxProperties();

    public function getMinProperties();

    public function getMaxItems();

    public function getMinItems();

    public function getUniqueItems();

    public function getPattern();

    public function getProperties();

    public function getMultipleOf();

    public function getMaximum();

    public function getExclusiveMaximum();

    public function getMinimum();

    public function getExclusiveMinimum();

    public function getAdditionalProperties();

    public function getPatternProperties();

    public function getType();

    public function getDefault();

    public function getFormat();

    public function getEnum();

    public function getRef();

    public function getRequired();

    public function getTitle();

    public function getDescription();

    public function getDefinition($name);

    public function getDefinitions();

    /**
     * The schema version, e.g. http://json-schema.org/draft-04/schema#
     * @return string
     */
    public function getSchema();

    public function setId($id);

    public function setSchema($schema);

    public function setTitle($title);

    public function setDescription($description);

    public function setDefinitions(array $definitions);

    /**
     * Returns the JSON Schema object as an array
     * @return array
     */
    public function toArray();
}