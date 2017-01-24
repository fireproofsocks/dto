<?php

namespace spec\Dto\Validators;

use Dto\Exceptions\InvalidEnumException;
use Dto\JsonSchema;
use Dto\Validators\EnumValidator;
use PhpSpec\ObjectBehavior;

class EnumValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(new JsonSchema());
        $this->shouldHaveType(EnumValidator::class);
    }

    function it_should_allow_values_when_enum_is_not_defined()
    {
        $this->beConstructedWith(new JsonSchema());
        $this->validate('anything goes')->shouldReturn(true);
    }

    function it_should_pass_validatation_when_the_value_under_examination_matches_a_value_in_the_enum_array()
    {
        $this->beConstructedWith(new JsonSchema(['enum' => ['horse', 'cat', 'hay']]));
        $this->validate('hay')->shouldReturn(true);
    }

    function it_should_fail_validatation_when_the_value_under_examination_is_not_in_the_enum_array()
    {
        $this->beConstructedWith(new JsonSchema(['enum' => ['horse', 'cat', 'hay']]));
        $this->shouldThrow(InvalidEnumException::class)->duringValidate('pig');
    }

    function it_should_fail_validatation_when_the_value_under_examination_is_not_the_same_type_as_what_is_in_the_enum_array()
    {
        $this->beConstructedWith(new JsonSchema(['enum' => [22, 32, 42]]));
        $this->shouldThrow(InvalidEnumException::class)->duringValidate("42");
    }
}
