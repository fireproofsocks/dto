<?php

namespace Dto;

use Dto\Exceptions\InvalidItemException;
use Dto\Exceptions\InvalidPropertyException;
use Dto\Exceptions\JsonSchemaFileNotFoundException;
use Dto\Validators\ArrayValidator;
use Dto\Validators\NumberValidator;
use Dto\Validators\ObjectValidator;
use Dto\Validators\StringValidator;
use Dto\Validators\ValidatorInterface;

/**
 * Class Schema
 *
 * PHP representation of the options available to the JSON-Schema spec.
 *
 * @link http://json-schema.org/latest/json-schema-core.html
 * @link http://json-schema.org/latest/json-schema-validation.html
 * @package Dto
 */
class JsonSchema implements RegulatorInterface
{

    /**
     * @var array
     */
    protected $schema;

    /**
     * @var array
     */
    protected $default_schema;

    /**
     * @var TypeDetectorInterface
     */
    protected $detector;

    /**
     * @var TypeConverterInterface
     */
    protected $converter;

    /**
     * @var FormatterInterface
     */
    protected $formatter;

    /**
     * @var ValidatorInterface
     */
    protected $stringValidator;

    /**
     * @var ValidatorInterface
     */
    protected $numberValidator;

    /**
     * @var ValidatorInterface
     */
    protected $objectValidator;

    /**
     * @var ValidatorInterface
     */
    protected $arrayValidator;


    public function __construct($input = null)
    {
        $this->schema = $this->constructSchema($input);
        $this->detector = new TypeDetector();
        $this->converter = new TypeConverter();

        $this->stringValidator = new StringValidator($this);
        $this->numberValidator = new NumberValidator($this);
        $this->objectValidator = new ObjectValidator($this);
        $this->arrayValidator = new ArrayValidator($this);
    }


    /**
     * Schema data can be loaded in different ways.
     *
     *  1. PHP Array is injected
     *  2. Name of JSON schema file is injected
     *
     * @param $input mixed
     * @return array
     */
    protected function constructSchema($input)
    {
        if (is_null($input)) {
            $input = include 'default_root_schema.php';
        }
        elseif (!is_array($input)) {
            $input = $this->getJsonFileContents($input);
        }

        return $input;
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

    public function getPropertySchemaAsArray($propertyName = null)
    {
        // Root level schema?
        if (is_null($propertyName)) {
            return $this->schema;
        }

        // TODO: check for $ref

        if ($this->propertyExists($propertyName)) {
            return $this->schema['properties'][$propertyName];
        }

        $additionalProperties = $this->getAdditionalProperties();

        // If "additionalProperties" is true, validation always succeeds.
        if ($additionalProperties === true) {
            return [];
        }

        $schema = $this->getSchemaByPatternProperties($propertyName);

        // Use the schema defined by patternProperties (which may be an empty array)
        if (is_array($schema)) {
            return $schema;
        }

        // Fall back to additionalProperties if it defines a schema (which may be an empty array)
        if (is_array($additionalProperties)) {
            return $additionalProperties;
        }

        throw new InvalidPropertyException('The property "'.$propertyName.'" is not allowed by the current schema.');
    }

    /**
     * For defining array schemas
     * If "items" is an array of schemas, it defines a "tuple" where the first item in the array must validate against
     * the first schema in "items", the 2nd item in the array against the 2nd schema, etc.
     * If the length of the array being stored is longer than the array of items, then we fall back to additionalItems.
     * When additionalItems is a schema, all additional values being stored are validated against that schema.
     *
     * @param null $index
     * @return array
     * @throws InvalidItemException
     */
    public function getItemSchemaAsArray($index = null)
    {
        $items = $this->getItems();

        if (empty($items)) {
            return $items; // empty schema
        }

        // Is this a true array of schemas? i.e. is this a tuple?
        if ($this->detector->isArray($items)) {
            if (isset($items[$index])) {
                return $items[$index];
            }
            // We're past the length of the defined
            $additionalItems = $this->getAdditionalItems();
            if ($additionalItems === false) {
                throw new InvalidItemException('The '.$index.'th item is not allowed by the current schema.');
            }
            // Section 5.9 seems like a smelly part of the schema:
            // "if the value of "additionalItems" is boolean value true or an object, validation of the instance always succeeds;"
            // Instead, what would make more sense(?) for cases where additionalItems contained a schema, would be that
            // all additional items have to be validated against that schema.
            elseif ($additionalItems === true) {
                return []; // empty schema (no restrictions)
            }
            else {
                return $additionalItems; // the schema
            }
        }
        else {
            return $items;
        }

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

    protected function propertyExists($name)
    {
        return isset($this->schema['properties'][$name]);
    }

    protected function getSchemaByPatternProperties($name)
    {
        foreach ($this->getPatternProperties() as $regex => $schema) {
            if (preg_match('/'.$regex.'/', $name)) {
                return $schema;
            }
        }

        return false;
    }

    public function isObject()
    {
        return in_array('object', $this->getTypeAsArray());
    }

    public function isArray()
    {
        return in_array('array', $this->getTypeAsArray());
    }

    public function isScalar()
    {
        $types = $this->getTypeAsArray();
        foreach ($types as $t) {
            if (in_array($t, ['string', 'integer', 'number', 'boolean', 'null'])) {
                return true;
            }
        }
        return false;
    }

    public function isValidObject($object)
    {
        // TODO: Implement isValidObject() method.
    }

    public function isValidArray($array)
    {
        // TODO: Implement isValidArray() method.
    }

    public function checkValidScalar($scalar)
    {
        if (is_string($scalar)) {
            return $this->stringValidator->validate($scalar);
        }
        elseif (is_int($scalar) || is_float($scalar)) {
            return $this->numberValidator->validate($scalar);
        }

        // TODO: check Formatter

        return $scalar;
    }

    public function checkValidObject($value) {
        return $this->objectValidator->validate($value);
    }

    public function checkValidArray($value) {
        return $this->arrayValidator->validate($value);
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

    public function getStorableTypeByValue($value)
    {
        $types = $this->getTypeAsArray();
        foreach ($types as $t) {
            if ($this->detector->{'is' . $t}($value)) {
                return $t;
            }
        }

        return false;
    }

    protected function getAdditionalProperties()
    {
        return (array_key_exists('additionalProperties', $this->schema)) ? $this->schema['additionalProperties'] : [];
    }

    protected function getPatternProperties()
    {
        return (array_key_exists('patternProperties', $this->schema)) ? $this->schema['patternProperties'] : [];
    }

    protected function getTypeAsArray()
    {
        $type = $this->getType();

        return (is_array($type)) ? $type : [$type];
    }

    /**
     * No type infers no constraints!
     * @return mixed|string
     */
    public function getType()
    {
        return (isset($this->schema['type'])) ? $this->schema['type'] : '';
    }

    public function isSingleType()
    {
        return (is_array($this->getType())) ? false : true;
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