<?php

namespace DtoTest;

use Dto\JsonSchemaAccessor;
use Dto\JsonSchemaAcessorInterface;

class JsonSchemaAccessorTest extends TestCase
{
    protected function getInstance($schema = null)
    {
        $container = include dirname(dirname(__DIR__)).'/src/container.php';

        // return new JsonSchemaAccessor($container, $schema);
        if (!is_null($schema)) {
            $container[JsonSchemaAcessorInterface::class]->set($schema);
        }

        return $container[JsonSchemaAcessorInterface::class];
    }

    public function testInstantiation()
    {
        $j = $this->getInstance();
        $this->assertInstanceOf(JsonSchemaAcessorInterface::class, $j);
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

}