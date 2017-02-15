<?php

namespace DtoTest\Regulator;

use Dto\DtoInterface;
use Dto\JsonSchemaAccessor;
use Dto\JsonSchemaAccessorInterface;
use Dto\JsonSchemaRegulator;
use Dto\RegulatorInterface;
use DtoTest\TestCase;
use Pimple\Container;

class GetDefaultTest extends TestCase
{
    protected function getMockContainer($default = null)
    {
        $container = new Container();
        $container[JsonSchemaAccessorInterface::class] = function ($c) use ($default) {
            return \Mockery::mock(JsonSchemaAccessor::class)
                ->shouldReceive('load')
                ->andReturnSelf()
                ->shouldReceive('getDefault')
                ->andReturn($default)
                ->getMock();
        };

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

    public function testResolvesScalarDtoInput()
    {
        $dto = new MockDto('my-string', true);

        $r = $this->getInstance(null);
        $result = $r->getDefault($dto);
        $this->assertEquals('my-string', $result);
    }

    public function testResolvesArrayDtoInput()
    {
        $dto = new MockDto(['a','b','c'], false);

        $r = $this->getInstance(null);
        $result = $r->getDefault($dto);
        $this->assertEquals(['a','b','c'], $result);
    }

    public function testResolvesStdClassObject()
    {
        $input = new \stdClass();
        $input->a = 'apple';

        $r = $this->getInstance(null);
        $result = $r->getDefault($input);
        $this->assertEquals(['a' => 'apple'], $result);
    }

    public function testGetDefaultDefersToInput()
    {
        $r = $this->getInstance('my-default');
        $result = $r->getDefault('override');
        $this->assertEquals('override', $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentWhenInputIsArrayAndDefaultIsString()
    {
        $r = $this->getInstance('my string');
        $r->getDefault(['a' => 'apple']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentWhenInputIsStringAndDefaultIsArray()
    {
        $r = $this->getInstance(['a' => 'apple']);
        $r->getDefault('my string');
    }

    public function testArraysAreMergedWhenInputIsAssociativeArrayAndDefaultIsAssociativeArray()
    {
        $r = $this->getInstance(['a' => 'apple','b' =>'boy']);
        $result = $r->getDefault(['a' => 'changed']);
        $this->assertEquals(['a' => 'changed','b' =>'boy'], $result);
    }

}

class MockDto implements DtoInterface
{
    protected $value;

    protected $isScalar;

    public function __construct($value, $isScalar = false)
    {
        $this->value = $value;
        $this->isScalar = $isScalar;
    }

    public function hydrate($value)
    {

    }

    public function set($index, $value)
    {

    }

    public function get($index)
    {

    }

    public function forget($index)
    {

    }

    public function exists($index)
    {

    }

    public function getSchema()
    {

    }

    public function toObject()
    {

    }

    public function toArray()
    {
        return $this->value;
    }

    public function toJson()
    {

    }

    public function toScalar()
    {
        return $this->value;
    }

    public function isScalar()
    {
        return $this->isScalar;
    }

}