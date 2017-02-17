<?php
namespace DtoTest\Validators;

use Dto\ServiceContainer;
use Dto\Validators\AnyOfValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class AnyOfValidatorTest extends TestCase
{
    protected function getInstance()
    {
        return new AnyOfValidator(new ServiceContainer());
    }

    public function testInstantiation()
    {
        $v = $this->getInstance();
        $this->assertInstanceOf(ValidatorInterface::class, $v);
    }

    public function testAnyMatchWillDo()
    {
        $v = $this->getInstance();

        $schema = [
            'anyOf' => [
                [
                    'type' => 'integer'
                ],
                [
                    'type' => 'string'
                ]
            ]
        ];

        $result = $v->validate(123, $schema);
        $this->assertEquals(123, $result);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidAnyOfException
     */
    public function testExceptionIsThrownWhenValueDoesntMatchAnyOfSchemas()
    {
        $v = $this->getInstance();

        $schema = [
            'anyOf' => [
                [
                    'type' => 'integer'
                ],
                [
                    'type' => 'string'
                ]
            ]
        ];

        // Uh oh... we don't want to do any type-casting for this...
        $result = $v->validate(false, $schema);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidAnyOfException
     */
    public function testNoMatchingObjectSchemaFound()
    {
        $v = $this->getInstance();

        $schema = [
            'anyOf' => [
                [
                    'type' => 'object',
                    'minProperties' => 2,
                    'maxProperties' => 2,

                ],
                [
                    'type' => 'object',
                    'minProperties' => 4,
                    'maxProperties' => 4,
                ]
            ]
        ];

        $result = $v->validate(['a' => 'apple', 'b' => 'boy', 'c' => 'cat'], $schema);
    }

    public function testMatchingObjectSchemaFound()
    {
        $v = $this->getInstance();

        $schema = [
            'anyOf' => [
                [
                    'type' => 'object',
                    'minProperties' => 2,
                    'maxProperties' => 2,

                ],
                [
                    'type' => 'object',
                    'minProperties' => 4,
                    'maxProperties' => 4,
                ]
            ]
        ];

        $result = $v->validate(['a' => 'apple', 'b' => 'boy'], $schema);

        $this->assertEquals(['a' => 'apple', 'b' => 'boy'], $result);

        $result = $v->validate(['a' => 'apple', 'b' => 'boy', 'c' => 'cat', 'd' => 'dog'], $schema);

        $this->assertEquals(['a' => 'apple', 'b' => 'boy', 'c' => 'cat', 'd' => 'dog'], $result);
    }

    public function testMatchingArraySchemaFound()
    {
        $v = $this->getInstance();

        $schema = [
            'anyOf' => [
                [
                    'type' => 'array',
                    'minItems' => 2,
                    'maxItems' => 2,

                ],
                [
                    'type' => 'array',
                    'minItems' => 4,
                    'maxItems' => 4,
                ]
            ]
        ];

        $result = $v->validate(['a', 'b'], $schema);

        $this->assertEquals(['a', 'b'], $result);

        $result = $v->validate(['a', 'b', 'c', 'd'], $schema);

        $this->assertEquals(['a', 'b', 'c', 'd'], $result);
    }
}