<?php
namespace DtoTest\Tutorial;

use Dto\Dto;
use DtoTest\TestCase;

class ArrayTest extends TestCase
{

    public function testTuple()
    {
        $schema = [
            'type' => 'array',
            'items' => [
                ['type' => 'string'],
                ['type' => 'integer'],
                ['type' => 'boolean'],
            ],
            'additionalItems' => false
        ];

        $dto = new Dto([], $schema);
        $dto[] = 'Main St.';
        $dto[] = '123';
        $dto[] = true;

        $this->assertEquals(['Main St.', 123, true], $dto->toArray());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidIndexException
     */
    public function testTupleExceeded()
    {
        $schema = [
            'type' => 'array',
            'items' => [
                ['type' => 'string'],
                ['type' => 'integer'],
                ['type' => 'boolean'],
            ],
            'additionalItems' => false
        ];

        $dto = new Dto([], $schema);
        $dto[] = 'Main St.';
        $dto[] = '123';
        $dto[] = true;
        $dto[] = 'boom';
    }


    public function testTupleExceededWithNoRestrictions()
    {
        $schema = [
            'type' => 'array',
            'items' => [
                ['type' => 'string'],
                ['type' => 'integer'],
                ['type' => 'boolean'],
            ],
            'additionalItems' => true
        ];

        $dto = new Dto([], $schema);
        $dto[] = 'Main St.';
        $dto[] = '123';
        $dto[] = true;
        $dto[] = 'no problem';
        $dto[] = ['anything', 'goes'];

        $this->assertEquals(['Main St.', 123, true, 'no problem', ['anything', 'goes']], $dto->toArray());
    }

    public function testTupleExtendedBySchema()
    {
        $schema = [
            'type' => 'array',
            'items' => [
                ['type' => 'string'],
                ['type' => 'integer'],
            ],
            'additionalItems' => [
                'type' => 'boolean'
            ]
        ];

        $dto = new Dto([], $schema);
        $dto[] = 'Weezy';
        $dto[] = 565;
        $dto[] = true;
        $dto[] = false;

        $this->assertEquals(['Weezy', 565, true, false], $dto->toArray());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidArrayValueException
     */
    public function testMinItemsThrowsException()
    {
        $schema = [
            'type' => 'array',
            'minItems' => 3
        ];

        $dto = new Dto([], $schema);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidArrayValueException
     */
    public function testMinItemsKicksInWhenItemRemoved()
    {
        $schema = [
            'type' => 'array',
            'minItems' => 3
        ];

        $dto = new Dto(['a', 'b', 'c'], $schema);
        $dto->forget(0);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidIndexException
     */
    public function testMaxItemsThrowsExceptionWhenExceeded()
    {
        $schema = [
            'type' => 'array',
            'maxItems' => 3
        ];

        $dto = new Dto([], $schema);
        $dto[] = 'one';
        $dto[] = 'two';
        $dto[] = 'three';
        $dto[] = 'BOOM';
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidArrayValueException
     */
    public function testUniqueItems()
    {
        $schema = [
            'type' => 'array',
            'uniqueItems' => true
        ];

        $dto = new Dto([], $schema);
        $dto[] = 'one';
        $dto[] = 'two';
        $dto[] = 'one'; // BOOM!
    }
}