<?php

namespace Dto;

/**
 * Class DtoStrict
 *
 * An alternative to the Dto class, this variation will throw the exceptions that are
 * raised when an operation attempts to write to a target location does not exist.
 *
 * @package Dto
 */
class DtoStrict extends Dto
{
    /**
     * @param \Exception $e
     * @throws \Exception
     */
    protected function handleException(\Exception $e) {
        throw $e;
    }
}