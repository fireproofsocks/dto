<?php

namespace spec\Dto\Validators;

use Dto\Exceptions\InvalidScalarValueException;
use Dto\JsonSchema;
use Dto\Validators\NumberValidator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NumberValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(new JsonSchema());
        $this->shouldHaveType(NumberValidator::class);
    }

    function it_is_valid_number_when_no_special_restrictions_are_defined()
    {
        $this->beConstructedWith(new JsonSchema());
        $this->validate(123)->shouldReturn(true);
    }

    // -------------------------------- multipleOf ----------------------------------------------
    function it_is_a_valid_number_when_number_is_multipleOf_the_given_value()
    {
        $this->beConstructedWith(new JsonSchema(['multipleOf' => 1.5]));
        $this->validate(3)->shouldReturn(true);
    }

    function it_is_not_a_valid_number_when_number_is_not_a_multipleOf_the_given_value()
    {
        $this->beConstructedWith(new JsonSchema(['multipleOf' => 1.5]));
        $this->shouldThrow(InvalidScalarValueException::class)->duringValidate(3.14);
    }

    // -------------------------------- maximum ----------------------------------------------
    function it_is_a_valid_number_when_it_is_less_than_the_defined_maximum()
    {
        $this->beConstructedWith(new JsonSchema(['maximum' => 42]));
        $this->validate(41)->shouldReturn(true);
    }

    function it_is_not_a_valid_number_when_it_is_greater_than_the_defined_maximum()
    {
        $this->beConstructedWith(new JsonSchema(['maximum' => 42]));
        $this->shouldThrow(InvalidScalarValueException::class)->duringValidate(43);
    }

    function it_is_a_valid_number_when_it_is_equal_to_the_defined_maximum()
    {
        $this->beConstructedWith(new JsonSchema(['maximum' => 42]));
        $this->validate(42)->shouldReturn(true);
    }

    // -------------------------------- exclusiveMaximum ----------------------------------------------
    function it_is_not_a_valid_number_when_it_is_equal_to_the_defined_maximum_and_exclusiveMaximum_is_true()
    {
        $this->beConstructedWith(new JsonSchema(['maximum' => 42, 'exclusiveMaximum' => true]));
        $this->shouldThrow(InvalidScalarValueException::class)->duringValidate(42);
    }

    // -------------------------------- minimum ----------------------------------------------
    function it_is_not_a_valid_number_when_it_is_less_the_defined_minimum()
    {
        $this->beConstructedWith(new JsonSchema(['minimum' => 33]));
        $this->shouldThrow(InvalidScalarValueException::class)->duringValidate(32);
    }

    function it_is_a_valid_number_when_it_is_equal_to_the_defined_minimum()
    {
        $this->beConstructedWith(new JsonSchema(['minimum' => 33]));
        $this->validate(33)->shouldReturn(true);
    }

    // -------------------------------- exclusiveMinimum ----------------------------------------------
    function it_is_a_valid_number_when_it_is_equal_to_the_defined_minimum_and_exclusiveMinimum_is_true()
    {
        $this->beConstructedWith(new JsonSchema(['minimum' => 33, 'exclusiveMinimum' => true]));
        $this->shouldThrow(InvalidScalarValueException::class)->duringValidate(33);
    }
}
