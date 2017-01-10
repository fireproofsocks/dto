<?php

namespace spec\Dto;

use Dto\Dto;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DtoSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Dto::class);
    }

    function it_can_be_represented_as_an_array()
    {
        $this->toArray()->shouldReturn([]);
    }

    function it_can_be_represented_as_json()
    {
        $this->toJson()->shouldReturn('[]');
    }

    function it_can_be_represented_as_an_object()
    {
        // For some reason, shouldReturn and others do not pass the test.
        $this->toObject()->shouldBeLike(new \stdClass());
    }


}
