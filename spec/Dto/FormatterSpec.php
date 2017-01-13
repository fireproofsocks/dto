<?php

namespace spec\Dto;

use Dto\Formatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Formatter::class);
    }
}
