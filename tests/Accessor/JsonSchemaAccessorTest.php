<?php

namespace DtoTest;

use Dto\JsonSchemaAccessor;
use Dto\JsonSchemaAccessorInterface;
use Dto\ServiceContainer;

class JsonSchemaAccessorTest extends TestCase
{
    protected function getInstance($schema = [])
    {
        $container = new ServiceContainer();

        return $container->make(JsonSchemaAccessorInterface::class)->factory($schema);

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
            'foo' => ['id' => 'subschema', 'title' => 'bar']
        ]]);
        $result = $j->getDefinition('foo');
        $this->assertEquals($result, ['id' => 'subschema', 'title' => 'bar']);
    }

    public function testGetDefinitionMergesMetaDataWhenItDoesNotHaveAnId()
    {
        $j = $this->getInstance([
            'id' => 'root',
            'definitions'=>[
                'foo' => ['title' => 'bar']
            ]
        ]);
        $result = $j->getDefinition('foo');
        $this->assertEquals(['id' => 'root', 'title' => 'bar', 'definitions'=>['foo' => ['title' => 'bar']]], $result);
    }

    public function testInstantiationWorksNormallyWhenPassedAnArraySchema()
    {
        $a = new JsonSchemaAccessor();
        $this->assertEquals([], $a->toArray());
    }

    public function testGetAllOf()
    {
        $a = $this->getInstance();
        $this->assertEquals(false, $a->getAllOf());
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
    
    public function testSetId()
    {
        $a = $this->getInstance();
        $a->setId('xyz');
        $this->assertEquals('xyz', $a->getId());
    }
    
    public function testSetSchema()
    {
        $a = $this->getInstance();
        $a->setSchema('myschema');
        $this->assertEquals('myschema', $a->getSchema());
    }

    public function testSetTitle()
    {
        $a = $this->getInstance();
        $a->setTitle('the title');
        $this->assertEquals('the title', $a->getTitle());
    }

    public function testSetDescription()
    {
        $a = $this->getInstance();
        $a->setDescription('the desc');
        $this->assertEquals('the desc', $a->getDescription());
    }

}