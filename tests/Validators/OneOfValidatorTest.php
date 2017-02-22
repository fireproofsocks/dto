<?php
namespace DtoTest\Validators;

use Dto\ServiceContainer;
use Dto\Validators\OneOfValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class OneOfValidatorTest extends TestCase
{
    protected function getInstance()
    {
        return new OneOfValidator(new ServiceContainer());
    }

    public function testInstantiation()
    {
        $v = $this->getInstance();
        $this->assertInstanceOf(ValidatorInterface::class, $v);
    }

    public function testPassesWhenExactlyOneSchemaValidates()
    {
        $v = $this->getInstance();

        $schema = [
            'oneOf' => [
                [
                    'type' => 'integer',
                    'minimum' => 5
                ],
                [
                    'type' => 'integer',
                    'maximum' => 10
                ]
            ]
        ];

        $result = $v->validate(12, $schema);

        $this->assertEquals(12, $result);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidOneOfException
     */
    public function testExceptionThrownWhenTwoSchemasValidate()
    {
        $v = $this->getInstance();

        $schema = [
            'oneOf' => [
                [
                    'type' => 'integer',
                    'minimum' => 5
                ],
                [
                    'type' => 'integer',
                    'maximum' => 10
                ]
            ]
        ];

        $v->validate(7, $schema);
    }

    public function testArraySchema()
    {
        $v = $this->getInstance();

        $schema = [
            'oneOf' => [
                [
                    'type' => 'array',
                    'minItems' => 2
                ],
                [
                    'type' => 'array',
                    'maxItems' => 4
                ]
            ]
        ];

        $result = $v->validate(['a', 'b', 'c', 'd', 'e'], $schema);
        $this->assertEquals(['a', 'b', 'c', 'd', 'e'], $result);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidOneOfException
     */
    public function testArraySchemasThrowsExceptionWhen2SchemasAreMatched()
    {
        $v = $this->getInstance();

        $schema = [
            'oneOf' => [
                [
                    'type' => 'array',
                    'minItems' => 2
                ],
                [
                    'type' => 'array',
                    'maxItems' => 4
                ]
            ]
        ];

        $v->validate(['a', 'b', 'c'], $schema);
    }

    public function testObjectSchema()
    {
        $v = $this->getInstance();

        $schema = [
            'oneOf' => [
                [
                    'type' => 'object',
                    'minProperties' => 2
                ],
                [
                    'type' => 'object',
                    'maxProperties' => 3
                ]
            ]
        ];

        $result = $v->validate(['a' => 'apple', 'b' => 'boy', 'c' => 'cat', 'd' => 'dog'], $schema);
        $this->assertEquals(['a' => 'apple', 'b' => 'boy', 'c' => 'cat', 'd' => 'dog'], $result);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidOneOfException
     */
    public function testObjectSchemaThrowsExceptionWhenMultipleSchemasMatched()
    {
        $v = $this->getInstance();

        $schema = [
            'oneOf' => [
                [
                    'type' => 'object',
                    'minProperties' => 2
                ],
                [
                    'type' => 'object',
                    'maxProperties' => 3
                ]
            ]
        ];

        $result = $v->validate(['a' => 'apple', 'b' => 'boy', 'c' => 'cat'], $schema);
        $this->assertEquals(['a' => 'apple', 'b' => 'boy', 'c' => 'cat'], $result);
    }
}