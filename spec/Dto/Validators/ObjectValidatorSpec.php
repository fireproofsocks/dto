<?php

namespace spec\Dto\Validators;

use Dto\Exceptions\InvalidObjectValueException;
use Dto\JsonSchema;
use Dto\Validators\ObjectValidator;
use PhpSpec\ObjectBehavior;

class ObjectValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(new JsonSchema());
        $this->shouldHaveType(ObjectValidator::class);
    }

    function it_cannot_have_more_than_the_allowed_maxProperties()
    {
        $this->beConstructedWith(new JsonSchema(['maxProperties' => 2]));
        $this->shouldThrow(InvalidObjectValueException::class)->duringValidate(['a'=>'x1', 'b'=>'x2','c'=>'x3']);
    }

    function it_cannot_have_fewer_than_the_allowed_minProperties()
    {
        $this->beConstructedWith(new JsonSchema(['minProperties' => 3]));
        $this->shouldThrow(InvalidObjectValueException::class)->duringValidate(['a'=>'x1', 'b'=>'x2']);
    }

    function it_is_valid_when_an_object_has_a_number_of_properties_between_the_allowed_min_and_max()
    {
        $this->beConstructedWith(new JsonSchema(['minProperties' => 2, 'maxProperties' => 4]));
        $this->validate(['a'=>'x1', 'b'=>'x2','c'=>'x3'])->shouldReturn(true);
    }
}
