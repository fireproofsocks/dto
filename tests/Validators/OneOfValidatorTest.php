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
}