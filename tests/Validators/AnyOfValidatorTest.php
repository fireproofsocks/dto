<?php
namespace DtoTest\Validators;

use Dto\Validators\AnyOfValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class AnyOfValidatorTest extends TestCase
{
    protected function getInstance()
    {
        $container = include __DIR__ . '/../../src/container.php';
        return new AnyOfValidator($container);
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
    public function testExceptionIsThrown()
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
}