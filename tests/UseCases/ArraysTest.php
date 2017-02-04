<?php

namespace DtoTest\UseCases;

use Dto\Dto;
use DtoTest\TestCase;

class ArraysTest extends TestCase
{
    public function testArrayOfStrings()
    {
        $schema = [
            'type' => 'array',
            'items' => [
                'type' => 'string'
            ]
        ];

        $dto = new Dto(null, $schema);
        $dto[] = 'foo';
        $dto[] = 'bar';

        $this->assertEquals(['foo', 'bar'], $dto->toArray());
    }

    public function testArrayOfStringsPerformsTypeCasting()
    {
        $schema = [
            'type' => 'array',
            'items' => [
                'type' => 'string'
            ]
        ];

        $dto = new Dto(null, $schema);
        $dto[] = 123;
        $dto[] = 456;

        $this->assertEquals(['123', '456'], $dto->toArray());
    }

    public function testArrayOfObjects()
    {
        $schema = [
            'type' => 'array',
            'items' => [
                'type' => 'object',
                'properties' => [
                    'a' => ['type' => 'string'],
                    'b' => ['type' => 'string']
                ]
            ]
        ];

        $dto = new Dto(null, $schema);
        $dto[] = ['a' => 'apple', 'b' => 'banjo'];
        $dto[] = ['a' => 'ask', 'b' => 'bork'];


        $this->assertEquals([['a' => 'apple', 'b' => 'banjo'], ['a' => 'ask', 'b' => 'bork']], $dto->toArray());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidArrayValueException
     */
    public function testMinItemsEnforced()
    {
        $schema = [
            'type' => 'array',
            'items' => [
                'type' => 'string'
            ],
            'minItems' => 2
        ];

        $dto = new Dto(['one'], $schema);
    }

    public function testReplaceExistingIndex()
    {
        $schema = [
            'type' => 'array',
            'items' => [
                'type' => 'string'
            ]
        ];

        $dto = new Dto(['foo', 'bar'], $schema);
        $dto[0] = 'full';

        $this->assertEquals(['full', 'bar'], $dto->toArray());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidIndexException
     */
    public function testReplaceNonExistingIndexFails()
    {
        $schema = [
            'type' => 'array',
            'items' => [
                'type' => 'string'
            ]
        ];

        $dto = new Dto(['foo', 'bar'], $schema);
        $dto[3] = 'full';

        $this->assertEquals(['full', 'bar'], $dto->toArray());
    }


    public function test()
    {
        $schema = [
            'type' => 'array',
            'items' => [
                'type' => 'string'
            ]
        ];

        $dto = new Dto(['foo', 'bar'], $schema);
        $dto[] = 'see';

        print_r($dto);
    }

}