<?php

namespace DtoTest;

use Dto\JsonSchemaAccessor;
use Dto\JsonSchemaAccessorInterface;

class JsonSchemaAccessorTest extends TestCase
{
    protected function getInstance($schema = null)
    {
        $container = include dirname(dirname(__DIR__)).'/src/container.php';

        // return new JsonSchemaAccessor($container, $schema);
        if (!is_null($schema)) {
            $container[JsonSchemaAccessorInterface::class]->load($schema);
        }

        return $container[JsonSchemaAccessorInterface::class];
    }

    public function testInstantiation()
    {
        $j = $this->getInstance();
        $this->assertInstanceOf(JsonSchemaAccessorInterface::class, $j);
    }

    public function testEmptyReferenceReturnsFalse()
    {
        $j = $this->getInstance();
        $this->assertFalse($j->getRef());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidReferenceException
     */
    public function testGetRefThrowsExceptionForNonStringArguments()
    {
        $j = $this->getInstance(['$ref'=>[]]);
        $j->getRef();
    }

    public function testGetDefinition()
    {
        $j = $this->getInstance(['definitions'=>[
            'foo' => ['title' => 'bar']
        ]]);
        $result = $j->getDefinition('foo');
        $this->assertEquals($result, ['title' => 'bar']);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInstantiationThrowExceptionWhenNonArrayPassedAsSchema()
    {
        $container = include dirname(dirname(__DIR__)).'/src/container.php';
        $a = new JsonSchemaAccessor($container, 'this is not an array');
    }

    public function testInstantiationWorksNormallyWhenPassedAnArraySchema()
    {
        $container = include dirname(dirname(__DIR__)).'/src/container.php';
        $a = new JsonSchemaAccessor($container, []);
        $this->assertEquals([], $a->getSchema());
    }

    public function testGetAllOf()
    {
        $a = $this->getInstance();
        $this->assertEquals([], $a->getAllOf());
    }

    public function testGetAdditionalItems()
    {
        $a = $this->getInstance();
        $this->assertEquals([], $a->getAdditionalItems());
    }

    public function testGetDescription()
    {
        $a = $this->getInstance(['description' => 'hi there']);
        $this->assertEquals('hi there', $a->getDescription());
    }

    /**
     * @expectedException \Dto\Exceptions\DefinitionNotFoundException
     */
    public function testNonExistantDefinitionThrowsException()
    {
        $a = $this->getInstance();
        $a->getDefinition('does-not-exist');
    }

}