<?php
namespace DtoTest\Validators\Types;

use Dto\ServiceContainer;
use Dto\Validators\Types\NullValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class NullValidatorTest extends TestCase
{
    protected function getInstance()
    {
        return new NullValidator(new ServiceContainer());
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
        $v->validate('not null', []);
    }

    public function testNullIsNull()
    {
        $v = $this->getInstance();
        $actual = $v->validate(null, []);
        $this->assertNull($actual);
    }
}