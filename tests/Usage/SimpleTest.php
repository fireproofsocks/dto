<?php

namespace DtoTest\Usage;

use Dto\Dto;
use Dto\DtoInterface;
use DtoTest\TestCase;

class SimpleTest extends TestCase
{
    public function testConstructor()
    {
        $dto = new Dto(['foo' => 'bar']);
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $this->assertEquals(['foo' => 'bar'], $dto->toArray());
    }

    public function testHydrateWithObject()
    {
        $dto = new Dto();
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $dto->hydrate(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $dto->toArray());
    }

    public function testHydrateWithArray()
    {
        $dto = new Dto();
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $dto->hydrate(['a','b','c']);
        $this->assertEquals(['a','b','c'], $dto->toArray());
    }


    public function testHydrateWillPerformTypecasting()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'i' => ['type' => 'integer']
            ],
        ];
        $dto = new Dto(null, $schema);
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $dto->hydrate(['a' => 'apple', 'i' => '42']);
        $this->assertEquals(['a' => 'apple', 'i' => 42], $dto->toArray());

        $dto->set('a','amazing');

        $this->assertEquals(['a' => 'amazing', 'i' => 42], $dto->toArray());
    }

    public function testSetAutomaticallyDeepensStructure()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'i' => ['type' => 'integer']
            ],
        ];
        $dto = new Dto(null, $schema);
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $dto->set('a','amazing');

        $this->assertEquals(['a' => 'amazing'], $dto->toArray());

    }

    public function testSetUsingObjectNotationAutomaticallyDeepensStructure()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'i' => ['type' => 'integer']
            ],
        ];
        $dto = new Dto(null, $schema);
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $dto->a = 'amazing';

        $this->assertEquals(['a' => 'amazing'], $dto->toArray());
    }

    public function testSetUsingArrayNotationAutomaticallyDeepensStructure()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'i' => ['type' => 'integer']
            ],
        ];
        $dto = new Dto(null, $schema);
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $dto['a'] = 'amazing';

        $this->assertEquals(['a' => 'amazing'], $dto->toArray());
    }

    public function testX()
    {
        $schema = [
            'type' => 'array',
            'items' => ['type' => 'string']
        ];
        $dto = new Dto(null, $schema);
        $this->assertInstanceOf(DtoInterface::class, $dto);
        $dto[] = 'amazing';

        $this->assertEquals(['amazing'], $dto->toArray());
    }
}