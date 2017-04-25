<?php

namespace DtoTest\UseCases;

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


    public function testSimpleArray()
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

    public function testNonNullableStringsGetSetToEmptyString()
    {
        $dto = new X();

        $dto->name = 'Lars';
        $dto->email = 'some@email.com';
        $dto->email = null;

        $this->assertEquals('', $dto->email->toScalar());
        $this->assertEquals('', strval($dto->email));
    }

    public function testIntegersAreReturnedAsIntegers()
    {
        $dto = new Dto(null, [
            'type' => 'object',
            'properties' => [
                'i' => ['type' => 'integer']
            ]
        ]);
        $dto->i = 5;
        $this->assertEquals(5, strval($dto->i));

    }

    public function testX()
    {
        $data = [
            'x' => 'xray'
        ];
        $schema = null;
        $schema = [
            'type' => 'object'
        ];

        $dto = new Dto($data, $schema);

    }
}

class X extends Dto
{
    protected $schema = [
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string'],
            'email' => ['type' => 'string'],
        ]
    ];
}