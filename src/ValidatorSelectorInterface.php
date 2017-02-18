<?php

namespace Dto;

/**
 * Interface ValidatorSelectorInterface
 *
 * Collect any "top-level" validators into the queue.  These are validation words that apply to the root schema
 * being validated. The first pass of validators includes looking for the presence of "type", "anyOf", "enum" etc.
 * Those "top-level" validators point to other validators, e.g. "type" would point to a "string" or "integer" validator.
 *
 * @package Dto
 */
interface ValidatorSelectorInterface
{
    public function selectValidators(array $schema);
}