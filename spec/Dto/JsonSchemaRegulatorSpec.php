<?php

namespace spec\Dto;

use Dto\Exceptions\JsonSchemaFileNotFoundException;
use Dto\JsonSchemaRegulator;
use PhpSpec\ObjectBehavior;
use Pimple\Container;
use Prophecy\Argument;

class JsonSchemaRegulatorSpec extends ObjectBehavior
{
    protected function getContainer()
    {
        //$container = new Container();
        //return $container;
        return include __DIR__. '/../../src/container.php';
    }

    function it_is_initializable()
    {
        $this->beConstructedWith($this->getContainer());
        $this->shouldHaveType(JsonSchemaRegulator::class);
    }

    function it_can_accept_an_array_as_a_schema()
    {
        $this->beConstructedWith($this->getContainer());
        $this->setSchema([])->shouldReturn(null);
    }

    function it_will_look_for_json_files_during_setSchema()
    {
        $this->beConstructedWith($this->getContainer());
        $this->shouldThrow(JsonSchemaFileNotFoundException::class)->duringSetSchema('/this/json/file/does/not/exist.json');
    }

    function it_uses_the_input_value_as_the_default_when_the_default_is_null()
    {
        $this->beConstructedWith($this->getContainer());
        $this->getDefault('xyz')->shouldReturn('xyz');
    }
}
