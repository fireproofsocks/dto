<?php

namespace Dto;

use Dto\Exceptions\InvalidIndexException;
use Dto\Exceptions\InvalidKeyException;

class JsonSchemaRegulator implements RegulatorInterface
{
    /**
     * @var ServiceContainerInterface
     */
    protected $serviceContainer;

    /**
     * @var JsonSchemaAccessorInterface
     */
    protected $schemaAccessor;

    /**
     * @var array
     */
    protected $schema = [];

    protected $isObject;

    protected $isArray;

    protected $isScalar;

    public $calledClass;

    protected $compiled = [];

    public function __construct(ServiceContainerInterface $serviceContainer, $calledClass = null)
    {
        $this->serviceContainer = $serviceContainer;
        $this->schemaAccessor = $serviceContainer->make(JsonSchemaAccessorInterface::class);
        $this->calledClass = ($calledClass) ? $calledClass : Dto::class;
    }

    public function postValidate(DtoInterface $dto)
    {
        if ($dto->getStorageType() === 'object') {
            $this->serviceContainer->make('objectValidator')->validate($dto->toArray(), $dto->getSchema());
        }
        elseif ($dto->getStorageType() === 'array') {
            $this->serviceContainer->make('arrayValidator')->validate($dto->toArray(), $dto->getSchema());
        }
    }

    /**
     * Do validation on the root-level schema (including combining schemas)
     * @inheritDoc
     */
    public function rootFilter($value, array $schema = [], $do_typecasting = true)
    {
        $value = $this->unwrapValue($value);

        // detect primary validator (enum, oneOf, allOf, type
        $validators = $this->serviceContainer->make(ValidatorSelectorInterface::class)->selectValidators($schema);

        // throws Exceptions on errors
        foreach ($validators as $v) {
            $value = $v->validate($value, $schema, $do_typecasting);
        }

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
            $value = ($value->getStorageType() === 'scalar') ? $value->toScalar() : $value->toArray();
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

        $accessor = $this->schemaAccessor->factory($schema);
        $type = $accessor->getType();

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
        if ($this->serviceContainer->make(TypeDetectorInterface::class)->isArray($value)) {
            $this->isArray = true;
            return 'array';
        }
        elseif ($this->serviceContainer->make(TypeDetectorInterface::class)->isObject($value)) {
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
     * @param $schema array
     * @return mixed|null
     */
    public function getDefault($input = null, array $schema = [])
    {
        $default = $this->schemaAccessor->factory($schema)->getDefault();

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
     * @param $index integer
     * @param array $schema
     * @return array
     * @throws InvalidIndexException
     */
    protected function getSchemaAtIndex($index, array $schema)
    {
        $accessor = $this->schemaAccessor->factory($schema);

        if ($maxItems = $accessor->getMaxItems()) {
            if (($index + 1) > $maxItems) {
                throw new InvalidIndexException('Arrays with more than '.$maxItems.' items disallowed by "maxItems".');
            }
        }

        $items = $accessor->getItems();

        // Is it a regular schema?  Each item must validate against this schema.
        if ($this->serviceContainer->make(TypeDetectorInterface::class)->isObject($items)) {
            return $items;
        }

        // Is a tuple (an array of schemas)?
        if ($this->serviceContainer->make(TypeDetectorInterface::class)->isArray($items)) {
            if (isset($items[$index])) {
                return $items[$index];
            }
        }

        // We have exceeded the number of schemas defining the tuple...
        $additionalItems = $accessor->getAdditionalItems();
        if ($this->serviceContainer->make(TypeDetectorInterface::class)->isBoolean($additionalItems)) {
            if ($additionalItems === true) {
                return [];
            }
        }
        elseif ($this->serviceContainer->make(TypeDetectorInterface::class)->isObject($additionalItems)) {
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
    protected function getSchemaAtKey($key, array $schema)
    {
        $accessor = $this->schemaAccessor->factory($schema);

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
     * Let the ReferenceResolver class handle how any $ref's are resolved.  This
     * is called when the DTO is instantiated because the DTO contains the schema.
     *
     * @param $schema mixed
     * @param $base_dir string
     * @return array
     */
    public function compileSchema($schema = null, $base_dir = '')
    {
        // Non-cryptographic fingerprinting to help redundant schema compiling
        $key = md5(json_encode($schema));
        if (!isset($this->compiled[$key])) {
            $resolver = $this->serviceContainer->make(ReferenceResolverInterface::class);
            $working_base_dir = $resolver->getWorkingBaseDir();
            // Check for base_dir override
            $base_dir = ($working_base_dir) ? $working_base_dir : $base_dir;
            $this->schema = $resolver->resolveSchema($schema, $base_dir);
            $this->compiled[$key] = $this->schema;
        }
        else {
            $this->schema = $this->compiled[$key];
        }

        return $this->schema;
    }

    public function getFilteredValueForIndex($value, $index, array $schema)
    {
        $schemaAccessor = $this->schemaAccessor->factory($schema);
        return new $this->calledClass($value, $schemaAccessor->mergeMetaData($this->getSchemaAtIndex($index, $schema)), $this);
    }

    public function getFilteredValueForKey($value, $key, array $schema)
    {
        $schemaAccessor = $this->schemaAccessor->factory($schema);
        return new $this->calledClass($value, $schemaAccessor->mergeMetaData($this->getSchemaAtKey($key, $schema)), $this);
    }

    /**
     * @param $value
     * @param $schema
     * @return mixed array
     */
    public function filterArray($value, $schema)
    {
        return $this->serviceContainer->make('arrayValidator')->validate($value, $schema);
    }

    /**
     * @param $value
     * @param $schema
     * @return mixed associative array
     */
    public function filterObject($value, $schema)
    {
        return $this->serviceContainer->make('objectValidator')->validate($value, $schema);
    }
}