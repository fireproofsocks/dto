<?php

namespace Dto\Validators;

class AlwaysPassesValidator extends AbstractValidator implements ValidatorInterface
{
    public function validate($value)
    {
        return true;
    }
}