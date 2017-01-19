<?php

namespace spec\Dto\Validators;

use Dto\Exceptions\InvalidArrayValueException;
use Dto\JsonSchema;
use Dto\Validators\ArrayValidator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArrayValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(new JsonSchema());
        $this->shouldHaveType(ArrayValidator::class);
    }

    function it_cannot_have_more_than_the_allowed_maxItems()
    {
        $this->beConstructedWith(new JsonSchema(['maxItems' => 2]));
        $this->shouldThrow(InvalidArrayValueException::class)->duringValidate(['a','b','c']);
    }

    function it_cannot_have_fewer_than_the_allowed_minItems()
    {
        $this->beConstructedWith(new JsonSchema(['minItems' => 3]));
        $this->shouldThrow(InvalidArrayValueException::class)->duringValidate(['a','b']);
    }

    function it_cannot_have_duplicate_values_when_uniqueItems_is_true()
    {
        $this->beConstructedWith(new JsonSchema(['uniqueItems' => true]));
        $this->shouldThrow(InvalidArrayValueException::class)->duringValidate(['a','b','b']);
    }

    function it_is_valid_when_an_array_has_a_number_of_items_between_the_allowed_min_and_max()
    {
        $this->beConstructedWith(new JsonSchema(['minItems' => 2, 'maxItems' => 4]));
        $this->validate(['a','b','c'])->shouldReturn(true);
    }
}
