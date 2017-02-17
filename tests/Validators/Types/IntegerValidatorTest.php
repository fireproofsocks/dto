<?php
namespace DtoTest\Validators\Types;

use Dto\ServiceContainer;
use Dto\Validators\Types\IntegerValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class IntegerValidatorTest extends TestCase
{
    protected function getInstance()
    {
        return new IntegerValidator(new ServiceContainer());
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
        $v->validate('not a number', []);
    }

    public function testIntegerIsInteger()
    {
        $v = $this->getInstance();
        $result = $v->validate(42, []);
        $this->assertEquals(42, $result);
    }

}