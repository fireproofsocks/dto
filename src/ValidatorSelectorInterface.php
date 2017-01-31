<?php

namespace Dto;

interface ValidatorSelectorInterface
{
    public function selectValidators(array $schema);
}