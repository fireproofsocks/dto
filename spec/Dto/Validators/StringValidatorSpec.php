<?php

namespace spec\Dto\Validators;

use Dto\Exceptions\InvalidScalarValueException;
use Dto\JsonSchema;
use Dto\Validators\StringValidator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StringValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(new JsonSchema());
        $this->shouldHaveType(StringValidator::class);
    }

    function it_is_valid_scalar_when_no_special_restrictions_are_defined()
    {
        $this->beConstructedWith(new JsonSchema());
        $this->validate('my string')->shouldReturn(true);
    }

    function it_is_not_valid_string_when_length_exceeds_maxLength()
    {
        $this->beConstructedWith(new JsonSchema(['maxLength' => 5]));
        $this->shouldThrow(InvalidScalarValueException::class)->duringValidate('abcdef');
    }

    function it_is_not_valid_string_when_length_isnt_minLength()
    {
        $this->beConstructedWith(new JsonSchema(['minLength' => 5]));
        $this->shouldThrow(InvalidScalarValueException::class)->duringValidate('abcd');
    }

    function it_is_valid_string_when_value_does_match_regex_pattern()
    {
        $this->beConstructedWith(new JsonSchema(['pattern' => '\.pdf$']));
        $this->validate('somefile.pdf')->shouldReturn(true);
    }

    function it_is_not_valid_string_when_value_does_not_match_regex_pattern()
    {
        $this->beConstructedWith(new JsonSchema(['pattern' => '\.pdf$']));
        $this->shouldThrow(InvalidScalarValueException::class)->duringValidate('somefile.doc');
    }
}
