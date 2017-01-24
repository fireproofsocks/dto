<?php

namespace spec\Dto\Validators;

use Dto\Validators\TypeValidator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TypeValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TypeValidator::class);
    }
}
