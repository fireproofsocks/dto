<?php

namespace DtoTest\Base;

use DtoTest\TestCase;
use Dto\JsonSchemaAccessor;
use Dto\JsonSchemaAcessorInterface;
use Dto\JsonSchemaRegulator;
use Dto\RegulatorInterface;
use Pimple\Container;

class DtoTestCase extends TestCase
{
    protected function getMockServiceContainer($filtered_value = null, $type = 'object')
    {
        $container = new Container();

        $container[RegulatorInterface::class] = function ($c) use ($filtered_value, $type) {

            $isObject = ($type === 'object') ? true : false;
            $isArray = ($type === 'array') ? true : false;
            $isScalar = ($type === 'scalar') ? true : false;

            return \Mockery::mock(JsonSchemaRegulator::class)
                ->shouldReceive('setSchema')
                ->andReturn(null)
                ->shouldReceive('getDefault')
                ->andReturn(null)
                ->shouldReceive('getSchemaAtIndex')
                ->andReturn(null)
                ->shouldReceive('getSchemaAtKey')
                ->andReturn(null)
                ->shouldReceive('filter')
                ->andReturn($filtered_value)
                ->shouldReceive('isObject')
                ->andReturn($isObject)
                ->shouldReceive('isArray')
                ->andReturn($isArray)
                ->shouldReceive('isScalar')
                ->andReturn($isScalar)
                ->getMock();
        };

        $container[JsonSchemaAcessorInterface::class] = function ($c) {
            return new JsonSchemaAccessor();
        };

        return $container;
    }
}