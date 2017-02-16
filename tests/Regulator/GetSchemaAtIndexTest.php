<?php
namespace DtoTest\Regulator;

use Dto\Dto;
use DtoTest\TestCase;

class GetSchemaAtIndexTest extends TestCase
{
    /**
     * @expectedException \Dto\Exceptions\InvalidIndexException
     */
    public function testMaxItemsThrowsExceptionWhenArraySizeExceeded()
    {
        $dto = new Dto(null, [
            'type' => 'array',
            'maxItems' => 1,
        ]);

        $dto[] = 'one';
        $dto[] = 'two';
    }

    public function testEachItemMustValidateAgainstTheSchemaInItemsWhenItemsContainsASchema()
    {
        $dto = new Dto(null, [
            'type' => 'array',
            'items' => [
                'type' => 'number',
                'minimum' => 1,
                'maximum' => 10,
                'description' => 'Any number between 1 and 10'
            ]
        ]);

        $dto[] = 1;
        $dto[] = 2;

        $this->assertEquals([
            'type' => 'number',
            'minimum' => 1,
            'maximum' => 10,
            'description' => 'Any number between 1 and 10'
        ], $dto[0]->getSchema());
    }

    public function testTuple()
    {
        $dto = new Dto(null, [
            'type' => 'array',
            'items' => [
                [
                    'type' => 'string',
                    'description' => 'First'
                ],
                [
                    'type' => 'string',
                    'description' => 'Second'
                ]
            ]
        ]);

        $dto[] = 'one';
        $dto[] = 'two';

        $this->assertEquals([
            'type' => 'string',
            'description' => 'First'
        ], $dto[0]->getSchema());

        $this->assertEquals([
            'type' => 'string',
            'description' => 'Second'
        ], $dto[1]->getSchema());
    }

    public function testAdditionalItemsTrueAllowsAnythingToExceedeTuple()
    {
        $dto = new Dto(null, [
            'type' => 'array',
            'items' => [
                [
                    'type' => 'string',
                    'description' => 'First'
                ],
                [
                    'type' => 'string',
                    'description' => 'Second'
                ]
            ],
            'additionalItems' => true
        ]);

        $dto[] = 'one';
        $dto[] = 'two';
        $dto[] = 'three';
        $dto[] = 'four';

        $this->assertEquals([], $dto[2]->getSchema());

        $this->assertEquals([], $dto[3]->getSchema());
    }

    public function testAdditionalItemsExceedingTuple()
    {
        $dto = new Dto(null, [
            'type' => 'array',
            'items' => [
                [
                    'type' => 'string',
                    'description' => 'First'
                ],
                [
                    'type' => 'string',
                    'description' => 'Second'
                ]
            ],
            'additionalItems' => [
                'type' => 'string',
                'description' => 'Someone overbooked this tuple!'
            ]
        ]);

        $dto[] = 'one';
        $dto[] = 'two';
        $dto[] = 'three';
        $dto[] = 'four';

        $this->assertEquals([
            'type' => 'string',
            'description' => 'Someone overbooked this tuple!'
        ], $dto[2]->getSchema());

        $this->assertEquals([
            'type' => 'string',
            'description' => 'Someone overbooked this tuple!'
        ], $dto[3]->getSchema());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidIndexException
     */
    public function testExceptionThrownWhenTupleExceededAndAdditionalItemsIsInvalid()
    {
        $dto = new Dto(null, [
            'type' => 'array',
            'items' => [
                [
                    'type' => 'string',
                    'description' => 'First'
                ],
                [
                    'type' => 'string',
                    'description' => 'Second'
                ]
            ],
            'additionalItems' => 'not a valid schema or boolean'
        ]);

        $dto[] = 'one';
        $dto[] = 'two';
        $dto[] = 'three';
    }
}