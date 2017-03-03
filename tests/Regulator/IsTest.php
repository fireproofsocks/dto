<?php

namespace DtoTest\Regulator;

use Dto\JsonSchemaAccessor;
use Dto\JsonSchemaAccessorInterface;
use Dto\JsonSchemaRegulator;
use Dto\MetaDataMerger;
use Dto\MetaDataMergerInterface;
use Dto\RegulatorInterface;
use DtoTest\TestCase;

class IsTest extends TestCase
{
    protected function getMockContainer()
    {
        $container = new MockContainer();
        $container->bind(JsonSchemaAccessorInterface::class, function ($c) {
            return \Mockery::mock(JsonSchemaAccessor::class)
                ->shouldReceive('getDefault')
                ->andReturn(null)
                ->getMock();
        });
        $container->bind(MetaDataMergerInterface::class, function ($c) {
            return \Mockery::mock(MetaDataMerger::class)
                ->shouldReceive('mergeMetaData')
                ->andReturn([])
                ->getMock();
        });
        return $container;
    }

    protected function getInstance()
    {
        return new JsonSchemaRegulator($this->getMockContainer());
    }

    public function testInstantiation()
    {
        $r = $this->getInstance();
        $this->assertInstanceOf(RegulatorInterface::class, $r);
    }

    public function testIsObject()
    {
        $r = $this->getInstance();
        $this->assertNull($r->isObject());
    }

    public function testIsArray()
    {
        $r = $this->getInstance();
        $this->assertNull($r->isArray());
    }

    public function testIsScalar()
    {
        $r = $this->getInstance();
        $this->assertNull($r->isScalar());
    }
}