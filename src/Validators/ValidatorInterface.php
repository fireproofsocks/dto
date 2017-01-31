<?php

namespace Dto\Validators;

interface ValidatorInterface
{
    public function validate($value, array $schema);
}