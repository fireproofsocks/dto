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
        $j->getRef([]);
    }

    public function testGetRemoteReference()
    {
        $j = $this->getInstance();
        $schema = $this->callProtectedMethod($j, 'getRemoteReference', [__DIR__.'/data/sample.json']);
        $this->assertInstanceOf(JsonSchemaAcessorInterface::class, $schema);
        $this->assertEquals($schema->getDescription(), 'Test test');
    }

    /**
     * @expectedException \Dto\Exceptions\JsonSchemaFileNotFoundException
     */
    public function testGetRemoteReferenceOfMissingFileThrowsException()
    {
        $j = $this->getInstance();
        $this->callProtectedMethod($j, 'getRemoteReference', [__DIR__.'/data/does_not_exist.json']);
    }

    /**
     * @expectedException \Dto\Exceptions\JsonDecoderException
     */
    public function testGetRemoteReferenceOfMalformedJsonThrowsException()
    {
        $j = $this->getInstance();
        $this->callProtectedMethod($j, 'getRemoteReference', [__DIR__.'/data/bad.json']);
    }

    public function testGetPhpReference()
    {
        $j = $this->getInstance();
        $this->callProtectedMethod($j, 'getPhpReference', [__DIR__.'/data/bad.json']);
    }
}