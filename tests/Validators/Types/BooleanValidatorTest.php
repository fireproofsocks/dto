<?php
namespace DtoTest\Validators\Types;

use Dto\Validators\Types\BooleanValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class BooleanValidatorTest extends TestCase
{
    protected function getInstance()
    {
        $container = include __DIR__ . '/../../../src/container.php';
        return new BooleanValidator($container);
    }

    public function testInstantiation()
    {
        $v = $this->getInstance();
        $this->assertInstanceOf(ValidatorInterface::class, $v);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testCheckDataTypeThrowsExceptionOnString()
    {
        $v = $this->getInstance();
        $v->validate('not a boolean', []);
    }

    public function testTrueIsBoolean()
    {
        $v = $this->getInstance();
        $result = $v->validate(true, []);
        $this->assertTrue($result);
    }
}