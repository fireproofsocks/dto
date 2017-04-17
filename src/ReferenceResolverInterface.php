<?php

namespace Dto;

/**
 * Interface ReferenceResolverInterface
 *
 * One job: dereference schemas referenced by the "$ref" keyword.  These can be local or remote JSON files, other
 * DTO classes, or in-line schemas in an object's "definitions" object.
 *
 * @package Dto
 */
interface ReferenceResolverInterface
{
    public function resolveSchema($schema = null);

    /**
     * When dealing with relative paths to json schemas, we need to keep track of the working base directory.
     * @return string
     */
    public function getWorkingBaseDir();
}