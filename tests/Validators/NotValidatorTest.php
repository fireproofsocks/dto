<?php
namespace DtoTest\Validators;

use Dto\ServiceContainer;
use Dto\Validators\NotValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class NotValidatorTest extends TestCase
{
    protected function getInstance()
    {
        return new NotValidator(new ServiceContainer());
    }

    public function testInstantiation()
    {
        $v = $this->getInstance();
        $this->assertInstanceOf(ValidatorInterface::class, $v);
    }

    public function testPassesWhenSchemaDoesNotValidate()
    {
        $v = $this->getInstance();

        $schema = [
            'not' => [
                'type' => 'integer',
                'minimum' => 5,
                'maximum' => 10
            ]
        ];

        $result = $v->validate(12, $schema);

        $this->assertEquals(12, $result);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidNotException
     */
    public function testFailsWhenSchemaDoesValidate()
    {
        $v = $this->getInstance();

        $schema = [
            'not' => [
                'type' => 'integer',
                'minimum' => 5,
                'maximum' => 10
            ]
        ];

        $v->validate(7, $schema);
    }
    
    public function testArraySchemas()
    {
        $v = $this->getInstance();

        $schema = [
            'type' => 'array',
            'not' => [
                'type' => 'array',
                'minItems' => 1,
                'maxItems' => 3
            ]
        ];

        $result = $v->validate(['a', 'b', 'c', 'd'], $schema);
        $this->assertEquals(['a', 'b', 'c', 'd'], $result);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidNotException
     */
    public function testArraySchemasThrowsException()
    {
        $v = $this->getInstance();

        $schema = [
            'type' => 'array',
            'not' => [
                'type' => 'array',
                'minItems' => 1,
                'maxItems' => 3
            ]
        ];

        $result = $v->validate(['a', 'b', 'c'], $schema);
        $this->assertEquals(['a', 'b', 'c'], $result);
    }
}