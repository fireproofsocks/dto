<?php

namespace DtoTest\Base;

use Dto\Dto;
use Dto\DtoInterface;
use Dto\JsonSchemaAccessor;
use Dto\JsonSchemaAcessorInterface;
use Dto\JsonSchemaRegulator;
use Dto\RegulatorInterface;
use DtoTest\TestCase;
use Pimple\Container;

class DtoTest extends TestCase
{
    protected function getMockServiceContainer()
    {
        $container = new Container();

        $container[RegulatorInterface::class] = function ($c) {
            return \Mockery::mock(JsonSchemaRegulator::class)
                ->shouldReceive('setSchema')
                ->andReturn(null)
                ->shouldReceive('getDefault')
                ->andReturn(null)
                ->shouldReceive('validate')
                ->andReturn(null)
                ->shouldReceive('isObject')
                ->andReturn(null)
                ->shouldReceive('isArray')
                ->andReturn(null)
                ->shouldReceive('isScalar')
                ->andReturn(null)
                ->getMock();
        };

        $container[JsonSchemaAcessorInterface::class] = function ($c) {
            return new JsonSchemaAccessor();
        };

        return $container;
    }

    public function testInstantiation()
    {
        $dto = new Dto(null, null, $this->getMockServiceContainer());
        $this->assertInstanceOf(DtoInterface::class, $dto);
    }
}