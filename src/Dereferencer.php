<?php

namespace Dto;

use Dto\Exceptions\InvalidReferenceException;
use Dto\Exceptions\JsonSchemaFileNotFoundException;

class Dereferencer implements DereferencerInterface
{
    protected $serviceContainer;

    public function __construct(\ArrayAccess $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    public function resolveReference($string)
    {
        if (!is_string($string)) {
            throw new InvalidReferenceException('The "$ref" parameter must store a string.');
        }
        // Get local definition
        if ('#' === substr($string, 0, 1)) {
            return $this->getLocalReference($string);
        }
        elseif (class_exists($string)) {
            return $this->getPhpReference($string);
        }

        return $this->getRemoteReference($string);
        // is PHP classname?
        // is JSON file?
        //$this->schema['$ref'];
    }

    protected function getLocalReference($ref)
    {
        // local definition - shift off the first part of the slash
        $relpath = ltrim($ref, '#/');
    }

    protected function getRemoteReference($ref)
    {
        $contents = @file_get_contents($ref);
        if ($contents === false) {
            throw new JsonSchemaFileNotFoundException('Could not open file at '. $ref);
        }

        $decoded = $this->serviceContainer[JsonDecoderInterface::class]->decode($contents);

        $this->serviceContainer[JsonSchemaAcessorInterface::class]->set($decoded);

        return $this->serviceContainer[JsonSchemaAcessorInterface::class];

    }

    protected function getPhpReference($ref)
    {

    }
}