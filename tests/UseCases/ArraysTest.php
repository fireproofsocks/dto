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

}