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
    protected function getMockRegulator($filter = null, $type = 'object', $default = null)
    {
        $isObject = ($type === 'object') ? true : false;
        $isArray = ($type === 'array') ? true : false;
        $isScalar = ($type === 'scalar') ? true : false;

        return \Mockery::mock(RegulatorInterface::class)
            ->shouldReceive('setSchema')
            ->andReturn(['title' => 'Testy test'])
            ->shouldReceive('getDefault')
            ->andReturn($default)
            ->shouldReceive('filter')
            ->andReturn($filter)
            ->shouldReceive('isObject')
            ->andReturn($isObject)
            ->shouldReceive('isArray')
            ->andReturn($isArray)
            ->shouldReceive('isScalar')
            ->andReturn($isScalar)
            ->shouldReceive('getSchemaAtIndex')
            ->andReturn([])

            ->getMock();
    }
}