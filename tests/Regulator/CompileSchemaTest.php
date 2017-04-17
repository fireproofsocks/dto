<?php

namespace DtoTest\Regulator;

use Dto\JsonSchemaAccessor;
use Dto\JsonSchemaAccessorInterface;
use Dto\JsonSchemaRegulator;
use Dto\MetaDataMerger;
use Dto\MetaDataMergerInterface;
use Dto\RegulatorInterface;
use Dto\ReferenceResolver;
use Dto\ReferenceResolverInterface;
use DtoTest\TestCase;

class CompileSchemaTest extends TestCase
{
    protected function getMockContainer($schema = [])
    {
        $container = new MockContainer();

        $container->bind(JsonSchemaAccessorInterface::class, function ($c) {
            return \Mockery::mock(JsonSchemaAccessor::class)
                ->shouldReceive('load')
                ->andReturn(null)
                ->shouldReceive('getDefault')
                ->andReturn(null)
                ->shouldReceive('set')
                ->andReturn(null)
                ->getMock();
        });

        $container->bind(ReferenceResolverInterface::class, function ($c) use ($schema) {
            return \Mockery::mock(ReferenceResolver::class)
                ->shouldReceive('resolveSchema')
                ->andReturn($schema)
                ->shouldReceive('getWorkingBaseDir')
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

    protected function getInstance($default = null)
    {
        return new JsonSchemaRegulator($this->getMockContainer($default));
    }

    public function testInstantiation()
    {
        $r = $this->getInstance();
        $this->assertInstanceOf(RegulatorInterface::class, $r);
    }

    public function testPassthru()
    {
        $r = $this->getInstance(['my' => 'schema']);
        $schema = $r->compileSchema('ignored -- see ReferenceResolverInterface');
        $this->assertEquals(['my' => 'schema'], $schema);
    }
}