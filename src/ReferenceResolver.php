<?php

namespace Dto;

use Dto\Exceptions\InvalidReferenceException;
use Dto\Exceptions\InvalidSchemaException;


class ReferenceResolver implements ReferenceResolverInterface
{
    protected $serviceContainer;

    /**
     * @var JsonSchemaAccessorInterface
     */
    protected $schemaAccessor;

    /**
     * @var string
     */
    protected $rel_path;

    public function __construct(ServiceContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * Take a schema (including a null schema), and resolve it to an array.
     * @param null $schema
     * @param string $path_prefix used when recursively fetching schemas
     * @return array
     * @throws InvalidSchemaException
     */
    public function resolveSchema($schema = null, $path_prefix = '')
    {
        $schema = $this->convertToArray($schema);
        $this->schemaAccessor = $this->serviceContainer->make(JsonSchemaAccessorInterface::class)->factory($schema);

        return $this->resolveReference($schema, $path_prefix);
    }

    /**
     * Convert the raw $schema argument to PHP associative array
     * @param $schema mixed
     * @return array
     * @throws InvalidSchemaException
     */
    protected function convertToArray($schema)
    {
        if ($schema instanceof DtoInterface) {
            $schema = $schema->getSchema();
        }
        if (is_null($schema)) {
            return $this->getDefaultSchema();
        }
        elseif (!is_array($schema)) {
            throw new InvalidSchemaException('Schema could not be resolved.');
        }

        return $schema;
    }

    protected function resolveReference(array $schema, $path_prefix)
    {
        if (!$ref = $this->schemaAccessor->getRef()) {
            return $schema;
        }

        // Get local definition
        if ('#' === substr($ref, 0, 1)) {
            $schema = $this->getInlineSchema($ref);
            return $this->resolveSchema($schema);
        }
        elseif (class_exists($ref)) {
            $schema = $this->getPhpSchema($ref);
            return $this->resolveSchema($schema);
        }
        else {
            $fullpath = $this->getFullPath($ref, $path_prefix);
            $schema = $this->getRemoteSchema($fullpath);
            return $this->resolveSchema($schema, dirname($fullpath));
        }

    }

    protected function getDefaultSchema()
    {
        return [];
    }

    protected function getInlineSchema($ref)
    {
        // local definition - shift off the first part of the slash
        $relpath = ltrim($ref, '#/');
        $definition = ltrim(strstr($relpath, '/'), '/');
        return $this->schemaAccessor->getDefinition($definition);
    }

    protected function getFullPath($ref, $path_prefix)
    {
        return ($path_prefix) ? $path_prefix . '/' . $ref : $ref;
    }
    protected function getRemoteSchema($ref)
    {
        return $this->serviceContainer->make(JsonDecoderInterface::class)->decodeFile($ref);
    }

    protected function getPhpSchema($ref)
    {
        // TODO: can we sniff this without instantiation?
        $dto = new $ref();

        if ($dto instanceof DtoInterface) {
            return $dto->getSchema();
        }

        throw new InvalidReferenceException('Referenced classnames must implement DtoInterface');
    }
}