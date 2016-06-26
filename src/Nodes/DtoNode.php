<?php
namespace S6\DataTransfer\NodeType;

use S6\Contracts\Dto\DtoContract;
use S6\Contracts\Dto\DtoNodeDefinitionContract;
use S6\DataTransfer\DtoBase;

use ReflectionClass;

class DtoNode implements DtoNodeDefinitionContract
{
    protected $config = [
        'class' => null,
        // Passed to the constructor:
        'defaults' => [],
        'options' => [
            'only' => []
        ]
    ];

    protected $dto;
    protected $node_dto;

    public function __construct(DtoContract $dto, array $config)
    {
        if (!isset($config['class'])) {
            throw new \Exception('DtoNode definitions require "class" key.');
        }
        if(!class_exists($config['class'])) {
            throw new \Exception('Class not found: '. $config['class']);
        }
        if (!in_array('S6\Contracts\Dto\DtoContract', class_implements($config['class']))) {
            throw new \Exception('Dto classes must implement the S6\Contracts\Dto\DtoContract interface.');
        }

        $classname = $config['class'];

        // Read configurable options (allows values from original $this->options only)
        $config = array_intersect_key($config, $this->config);
        $this->config = $config + $this->config;
        $this->dto = $dto;
        $this->node_dto = $this->instantiateDto($classname, $this->config['defaults'], $this->config['options']);
    }


    /**
     * When a definition references another dto object, this fetches a new instance of it.
     * @param $classname string
     * @param $options array
     * @return mixed
     */
    protected function instantiateDto($classname, array $defaults=[], array $options=[])
    {
        $this->dto->log('Instantiating child DTO "'.$classname.'"' , 'info');
        $ref = new ReflectionClass($classname);
        if (!$ref->implementsInterface('S6\Contracts\Dto\DtoContract')) {
            throw new \InvalidArgumentException('Class ' . $classname . ' does not implement S6\Contracts\Dto\DtoContract');
        }

        return new $classname($defaults, $options);

    }

    /**
     * @param string $dotted_key
     * @param mixed $value
     * @param null $key (optional: if set, the location won't be appended to, it will be written to that spot)
     * @return bool
     */
    public function append($dotted_key, $value, $key = null)
    {
        $this->dto->log('Appending data to "'. $dotted_key.'"');

        $location = &$this->dto->get($dotted_key);

        // If we set the key, the location becomes a hash
        if (!is_array($location) || ($key === null && $this->dto->isHash($location))) {
            $this->dto->log('Cannot append to non-array at '.$dotted_key,'error');
            return false;
        }

        if ($key === null) {
            $location[] = $this->resolveDto($value);
            $this->dto->appendDefinitions($dotted_key, $this->node_dto->getDefinitions());
        }
        else {
            // TODO: getNormalizedKey to make sure the key doesn't contain invalid characters
            $location[$key] = $this->resolveDto($value);
            $altered_defs = $this->node_dto->getDefinitions();
            $altered_defs[$key] = $altered_defs;
            $this->dto->appendDefinitions($dotted_key, $altered_defs);
        }
        return true;
    }

    /**
     * This function resolves input $values to arrays so users can pass in DTO objects without needing to convert them
     * to arrays first.  This is a convenience method.
     *
     * @param $value mixed
     * @return array
     */
    public function resolveDto($value) {
        // Do a conversion if the input was a DTO object
        if ($value instanceof DtoBase) {
            return $value->toArray();
        }
        // Filter input $values by pushing it through the DTO methods
        else {
            $this->node_dto->fromArray($value); // populate with new data
            return $this->node_dto->toArray();
        }
    }
    /**
     * $dotted_key should reference the edge of the parent DTO.
     *
     * @param string $dotted_key
     * @param mixed $value
     * @return bool|void
     */
    public function set($dotted_key, $value)
    {
        // What exists at the parent already?
        $extant = $this->dto->toArray($dotted_key);
        $node_defaults = $this->node_dto->toArray();
        $value = $this->resolveDto($value);

        // Do not attempt to set this via $this->dto->set() : it will result in recursion!
        $location = &$this->dto->get($dotted_key);
        $location = array_merge($extant, $node_defaults, $value);

        $this->dto->appendDefinitions($dotted_key, $this->node_dto->getDefinitions());

        return true;
    }

    public function splice($dotted_key, $value, $offset)
    {
        $location = &$this->dto->get($dotted_key);

        if (!is_array($location) || $this->dto->isHash($location)) {
            $this->dto->log('Cannot append to non-array at '.$dotted_key,'error');
            return false;
        }

        if (empty($location)) {
            return $this->append($dotted_key, $value);
        }

        // GOTCHA: the replacement array (argument 4) must be wrapped in an array, otherwise the keys get mangled
        // and the result is not what you would think.
        array_splice($location, $offset, 0, [$this->resolveDto($value)]);

        $this->dto->appendDefinitions($dotted_key, $this->node_dto->getDefinitions());
        return true;
    }
}