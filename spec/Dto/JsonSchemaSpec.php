<?php

namespace spec\Dto;

use Dto\JsonSchema;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JsonSchemaSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(JsonSchema::class);
    }
}
