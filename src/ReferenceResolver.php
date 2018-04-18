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

    protected $workingBaseDir = null;

    protected $decodedCache = [];

    /**
     * @var string
     */
    protected $rel_path;

    public function __construct(ServiceContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * Take a schema (including a null schema), and resolve it to a PHP associative array.
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

    /**
     * $ref may be one of the following:
     *
     *      1. Local definition (starting with #), e.g. #/definitions/xyz
     *      2. Fully qualified name of class implementing DtoInterface
     *      3. "Remote" JSON file (including protocol), usually http://site.com/schema.json but also file:// etc.
     *      4. Absolute local path, e.g. /var/www/something/schema.json
     *      5. Relative path (relative to Dto::$baseDir), e.g.
     *
     * @param array $schema
     * @param $path_prefix string
     * @return array
     */
    protected function resolveReference(array $schema, $path_prefix)
    {
        if (!$ref = $this->schemaAccessor->getRef()) {
            return $schema;
        }

        // 1. local definition
        if ('#' === substr($ref, 0, 1)) {
            $schema = $this->getInlineSchema($ref);
            return $this->resolveSchema($schema);
        }
        // 2. Fully qualified classname
        elseif (class_exists($ref)) {
            $schema = $this->getPhpSchema($ref);
            return $this->resolveSchema($schema);
        }
        // 3. "Remote" JSON file (anything with a protocol scheme)
        elseif (parse_url($ref, PHP_URL_SCHEME)) {
            $schema = $this->getRemoteSchema($ref);
            return $this->resolveSchema($schema);
        }
        // 4. Absolute local path
        elseif ('/' === substr($ref, 0, 1)) {
            $fullpath = $this->getFullPath($ref);
            $schema = $this->getRemoteSchema($fullpath);
            $this->storeWorkingBaseDirectoryFromFullPath($fullpath);
            return $this->resolveSchema($schema, $this->workingBaseDir);
        }
        // 5. Relative path
        else {
            $fullpath = $this->getFullPath($ref, $path_prefix);
            $schema = $this->getRemoteSchema($fullpath);
            $this->storeWorkingBaseDirectoryFromFullPath($fullpath);
            return $this->resolveSchema($schema, $this->workingBaseDir);
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

    protected function getFullPath($ref, $path_prefix = null)
    {
        return ($path_prefix) ? $path_prefix . '/' . $ref : $ref;
    }

    protected function getRemoteSchema($ref)
    {
        if (!isset($this->decodedCache[$ref])) {
            $this->decodedCache[$ref] = $this->serviceContainer->make(JsonDecoderInterface::class)->decodeFile($ref);
        }

        return $this->decodedCache[$ref];
    }

    protected function getPhpSchema($ref)
    {
        // Instantiating the object would allow us to use the getSchema() method, but it also triggers validation
        $reflectionClass = new \ReflectionClass($ref);

        if ($reflectionClass->isSubclassOf(Dto::class)) {
            $properties = $reflectionClass->getDefaultProperties();
            return (array_key_exists('schema', $properties)) ? $properties['schema'] : [];
        }

        throw new InvalidReferenceException('Referenced classnames must implement DtoInterface');
    }

    protected function storeWorkingBaseDirectoryFromFullPath($fullpath)
    {
        $this->workingBaseDir = rtrim(dirname($fullpath), './');
    }

    public function getWorkingBaseDir()
    {
        return $this->workingBaseDir;
    }
}