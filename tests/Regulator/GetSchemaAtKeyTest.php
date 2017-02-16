<?php
namespace DtoTest\Regulator;

use Dto\Dto;
use DtoTest\TestCase;

class GetSchemaAtKeyTest extends TestCase
{
    public function testGetSchemaAtExplicitlyDefinedKey()
    {
        $dto = new Dto(null, [
            'type' => 'object',
            'properties' => [
                'a' => [
                    'type' => 'string',
                    'title' => 'Surprise!'
                ]
            ],
            'default' => [
                'a' => 'apple'
            ]
        ]);

        $this->assertEquals([
            'type' => 'string',
            'title' => 'Surprise!'
        ], $dto->a->getSchema());
    }

    public function testGetSchemaByPatternProperties()
    {
        $dto = new Dto(null, [
            'type' => 'object',
            'patternProperties' => [
                '^str_' => [
                    'type' => 'string',
                    'title' => 'Starts with str_'
                ],
                '^num_' => [
                    'type' => 'number',
                    'title' => 'Starts with num_'
                ],
            ],
            'default' => [
                'str_x' => '',
                'num_y' => 333
            ]
        ]);

        $this->assertEquals([
            'type' => 'string',
            'title' => 'Starts with str_'
        ], $dto->str_x->getSchema());

        $this->assertEquals([
            'type' => 'number',
            'title' => 'Starts with num_'
        ], $dto->num_y->getSchema());
    }

    public function testGetEmptySchemaWhenAdditionalPropertiesIsTrue()
    {
        $dto = new Dto(null, [
            'type' => 'object',
            'patternProperties' => [
                '^str_' => [
                    'type' => 'string',
                    'title' => 'Starts with str_'
                ],
                '^num_' => [
                    'type' => 'number',
                    'title' => 'Starts with num_'
                ],
            ],
            'additionalProperties' => true,
            'default' => [
                'str_x' => '',
                'num_y' => 333,
                'random' => 'anything here'
            ]
        ]);

        $this->assertEquals([], $dto->random->getSchema());

    }

    public function testGetAdditionalSchemaWhenAdditionalPropertiesIsASchema()
    {
        $dto = new Dto(null, [
            'type' => 'object',
            'patternProperties' => [
                '^str_' => [
                    'type' => 'string',
                    'title' => 'Starts with str_'
                ],
                '^num_' => [
                    'type' => 'number',
                    'title' => 'Starts with num_'
                ],
            ],
            'additionalProperties' => [
                'type' => 'string',
                'description' => 'Hiding out'
            ],
            'default' => [
                'str_x' => '',
                'num_y' => 333,
                'random' => 'anything here'
            ]
        ]);

        $this->assertEquals([
            'type' => 'string',
            'description' => 'Hiding out'
        ], $dto->random->getSchema());

    }
}