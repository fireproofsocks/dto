<?php

namespace Dto;

use Dto\Exceptions\JsonSchemaFileNotFoundException;

/**
 * Class Schema
 *
 * PHP representation of the options available to the JSON-Schema spec.
 *
 * See
 * http://json-schema.org/latest/json-schema-core.html
 * http://json-schema.org/latest/json-schema-validation.html
 * @package Dto
 */
class JsonSchema implements JsonSchemaInterface
{
//    /**
//     * I.e. the "$schema" keyword, an "absolute" URI
//     *
//     * @var string
//     */
//    protected $schema = 'http://json-schema.org/draft-04/schema#';
//
//    /**
//     * URI for the schema
//     * @var string
//     */
//    protected $id = '';
//
//    protected $title = '';
//
//    /**
//     * Human readable description of your schema
//     * @var string
//     */
//    protected $description = '';
//
//    /**
//     * Defines one type for the data (e.g. "object"), or a list of allowable types (e.g. ["array", "null"])
//     * string | array
//     * @var string
//     */
//    protected $type = '';
//
//    /**
//     * Only relevant when type=object
//     * @var array
//     */
//    protected $properties = [];
//
//    /**
//     * List of required properties
//     * @var array
//     */
//    protected $required = [];
//
//    /**
//     * Only relevant when type=array, this defines what each array element looks like
//     * @var array
//     */
//    protected $items = [];
//
//    /**
//     * In-line schema definitions
//     * @var array
//     */
//    protected $definitions = [];
//
//
//    protected $patternProperties = [];
//
//    /**
//     * Can additional ad-hoc properties be added?
//     * @var bool
//     */
//    protected $additionalProperties = false;
//

    /**
     * @var array
     */
    protected $data;


    protected $detector;

    protected $converter;

    protected $formatter;

    /**
     *
     * @var array
     */
    protected $schema_keywords = [
        // http://json-schema.org/latest/json-schema-core.html
        '$schema' => 'http://json-schema.org/draft-04/schema#',
        'id' => '',


        // http://json-schema.org/latest/json-schema-validation.html

        // http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.1
        'multipleOf' => 1,
        'maximum' => 0, // !!!
        'exclusiveMaximum' => true, // !!!
        'minimum' => 0, // !!!
        'exclusiveMinimum' => true, // !!!
        'maxLength' => 0, // !!!

        'minLength' => 0, // if absent, may be considered as being present with integer value 0.
        'pattern' => '', // !!!

        'additionalItems' => '', // MUST be either a boolean or an object
        'items' => [],

        'maxItems' => 0, // !!!
        'minItems' => 0, // !!!

        'uniqueItems' => false,

        'maxProperties' => 0, // !!!
        'minProperties' => 0,

        'required' => [], // !!! This array MUST have at least one element, must be unique

        'properties' => [],

        'patternProperties' => '',

        'additionalProperties' => '', // !!! MUST be a boolean or a schema.

        'dependencies' => [], // !!!
        'enum' => [], // !!!

        // http://json-schema.org/latest/json-schema-core.html#rfc.section.4.2
        'type' => '', // !!! can be an array
        'allOf' => [], // !!!
        'anyOf' => [], // !!!
        'oneOf' => [], // !!!
        'not' => [], // !!! schema

        // 5.26
        'definitions' => [],

        // Meta Data Keywords
        // http://json-schema.org/latest/json-schema-validation.html#rfc.section.6
        'title' => '',
        'description' => '',
        // http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.2
        'default' => [],

        // Semantic Validation with "format"
        // http://json-schema.org/latest/json-schema-validation.html#rfc.section.7
        'format' => '', // see $this->formats
    ];

    // for "type"
    protected $valid_types = [
        'null',
        'boolean',
        'object',
        'array',
        'number',
        'string',
        'integer' // integer JSON numbers SHOULD NOT be encoded with a fractional part.
    ];

    // for "format"
    protected $valid_formats = [
        'date-time',
        'email',
        'hostname',
        'ipv4',
        'ipv6',
        'uri',
        'uriref'
    ];


    public function __construct($input = null)
    {
        $this->constructSchema($input);
    }


    /**
     * Schema data can be loaded in different ways.
     *
     *  1. PHP Array is injected
     *  2. Name of JSON schema file is injected
     *
     * @param $input
     */
    protected function constructSchema($input)
    {
        if (!is_array($input)) {
            $input = $this->getJsonFileContents($input);
        }
        $this->constructSchemaFromArray($input);
    }

    protected function getJsonFileContents($filename_or_url) {
        $contents = file_get_contents($filename_or_url);
        if ($contents === false) {
            throw new JsonSchemaFileNotFoundException('JSON Schema not found: '. $filename_or_url);
        }

        $array = json_decode($contents, true);
        // Errors?
        return $array;
    }

    protected function constructSchemaFromArray(array $input)
    {
        // TODO: filter/validate the schema
        $this->data = $input;
    }

    public function toArray()
    {
        return $this->data;
    }

    public function toJson()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }


    public function get($key)
    {
        return $this->data[$key];
    }

    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    public function __get($key)
    {
        return $this->data[$key];
    }

    public function getTypeAsArray()
    {

    }

    /**
     * Either the property is explicitly defined in the $properties array, or $additionalProperties is set to true.
     * @param $index
     * @return boolean
     */
    public function isPropertySettable($index)
    {
        return ($this->additionalProperties || array_key_exists($index, $this->properties));
    }

    public function isAppendable()
    {

    }

    public function canHoldScalar()
    {

    }

    /**
     * @param $value mixed
     * @return bool
     */
    protected function isValueOneOfAllowedTypes($value)
    {
        // TODO: enum could be up here?

        // Type is usually a single value, but some may allow multiple types, esp. nullable, e.g. ["string", "null"]
        $types = (is_array($this->type)) ? $this->type : [$this->type];

        foreach ($types as $t) {
            switch ($t) {
                case 'object':
                    if ($this->detector->isObject($value)) {
                        return true;
                    }
                    break;
                case 'array':
                    if ($this->detector->isArray($value)) {
                        return true;
                    }
                    break;
                case 'string':
                    if ($this->detector->isString($value)) {
                        return true;
                    }
                    break;
                case 'integer':
                    if ($this->detector->isInteger($value)) {
                        return true;
                    }
                    break;
                case 'number':
                    if ($this->detector->isNumber($value)) {
                        return true;
                    }
                    break;
                case 'boolean':
                    if ($this->detector->isBoolean($value)) {
                        return true;
                    }
                    break;
                case 'null':
                    if ($this->detector->isNull($value)) {
                        return true;
                    }
                    break;
            }
        }

        return false;
    }

    protected function isAggregateType()
    {
        $type = $this->getPrimaryType();
        return ($type === 'object' || $type === 'array');
    }

    public function isObject()
    {

    }

    public function isArray()
    {

    }

    public function isScalar()
    {

    }

}