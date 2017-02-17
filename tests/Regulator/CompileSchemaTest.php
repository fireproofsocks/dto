<?php

namespace DtoTest\Regulator;

use Dto\JsonSchemaAccessor;
use Dto\JsonSchemaAccessorInterface;
use Dto\JsonSchemaRegulator;
use Dto\RegulatorInterface;
use Dto\Resolver;
use Dto\ResolverInterface;
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

        $container->bind(ResolverInterface::class, function ($c) use ($schema) {
            return \Mockery::mock(Resolver::class)
                ->shouldReceive('resolveSchema')
                ->andReturn($schema)
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
        $schema = $r->compileSchema('ignored -- see ResolverInterface');
        $this->assertEquals(['my' => 'schema'], $schema);
    }
}