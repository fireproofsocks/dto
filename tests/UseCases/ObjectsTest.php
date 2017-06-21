<?php
namespace DtoTest\UseCases;

use Dto\Dto;
use DtoTest\TestCase;

class ObjectsTest extends TestCase
{
    public function testRegularUsage()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'b' => ['type' => 'string']
            ],
            'additionalProperties' => false
        ];

        $dto = new Dto(['a' => 'apple', 'b' => 'banjo'], $schema);
        $this->assertEquals(['a' => 'apple', 'b' => 'banjo'], $dto->toArray());
    }


    /**
     * @expectedException \Dto\Exceptions\InvalidObjectValueException
     */
    public function testRequiredPropertiesIsEnforcedImmediately()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'b' => ['type' => 'string']
            ],
            'required' => ['a','b'],
            'additionalProperties' => false

        ];

        $dto = new Dto(['a' => 'apple'], $schema);
    }

    public function testDefaultValuesWorkForRequiredValues()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'b' => ['type' => 'string']
            ],
            'required' => ['a','b'],
            'additionalProperties' => false,
            'default' => [
                'a' => '',
                'b' => ''
            ]

        ];

        $dto = new Dto(['a' => 'apple'], $schema);
        $this->assertEquals(['a' => 'apple', 'b' => ''], $dto->toArray());
    }


    /**
     * @expectedException \Dto\Exceptions\InvalidKeyException
     */
    public function testTooManyPropertiesThrowsExceptionWhenAdditionalPropertiesIsFalse()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'b' => ['type' => 'string']
            ],
            'additionalProperties' => false

        ];

        $dto = new Dto(['a' => 'apple', 'b' => 'banjo', 'c' => 'not allowed'], $schema);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidObjectValueException
     */
    public function testMinPropertiesEnforced()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'b' => ['type' => 'string'],
                'c' => ['type' => 'string']
            ],
            'minProperties' => 2

        ];

        $dto = new Dto(['a' => 'apple'], $schema);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidArrayOperationException
     */
    public function testArrayAccessNotAllowedOnObjects()
    {
        $schema = [
            'type' => 'object'
        ];

        $dto = new Dto(null, $schema);
        $dto['a'] = 123;
    }

    public function testNullValidatorOnProperty()
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'a' => ['type' => 'string'],
                'b' => ['type' => 'null'],
            ]
        ];
        $dto = new Dto(null, $schema);

        $dto->a = 'my string';
        $dto->b = 'null me up';

        $this->assertEquals(['a' => 'my string', 'b' => null], $dto->toArray());
    }
}