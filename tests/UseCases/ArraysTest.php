<?php

namespace DtoTest\UseCases;

use Dto\Dto;
use DtoTest\TestCase;

class ArraysTest extends TestCase
{
    public function testPushingStringsOntoArray()
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


    /**
     * @expectedException \Dto\Exceptions\InvalidIndexException
     */
    public function testMaxItemsIsEnforcedWhenItemsAreAddedIndividually()
    {
        $schema = [
            'type' => 'array',
            'maxItems' => 2
        ];

        $dto = new Dto(null, $schema);
        $dto[] = 'one';
        $dto[] = 'two';
        $dto[] = 'boom';
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidIndexException
     */
    public function testMaxItemsIsEnforcedWhenItemsAreAddedToAFullArray()
    {
        $schema = [
            'type' => 'array',
            'maxItems' => 2
        ];

        $dto = new Dto(['foo', 'bar'], $schema);
        $dto[] = 'boom';
    }

    public function testReadFromIndex()
    {
        $schema = [
            'type' => 'array',
        ];

        $dto = new Dto(['zero', 'one'], $schema);

        $this->assertEquals('one', $dto[1]);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidIndexException
     */
    public function testReadFromNonExistantIndexThrowException()
    {
        $schema = [
            'type' => 'array',
        ];

        $dto = new Dto(['zero', 'one'], $schema);

        $dto[2];
    }

}