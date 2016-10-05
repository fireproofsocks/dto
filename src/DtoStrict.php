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
     * @param string $index the location being written to
     * @param mixed $value the problematic value
     * @throws \Exception
     */
    protected function handleException(\Exception $e, $index, $value)
    {
        throw $e;
    }
}