<?php
namespace DtoTest\Validators\Types;

use Dto\ServiceContainer;
use Dto\Validators\Types\NumberValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class NumberValidatorTest extends TestCase
{
    protected function getInstance()
    {
        return new NumberValidator(new ServiceContainer());
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

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testCheckMultipleOfThrowsExceptionWhenNumberNotDivisibleByInt()
    {
        $v = $this->getInstance();
        $v->validate(25, [
            'multipleOf' => 3
        ]);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testCheckMultipleOfThrowsExceptionWhenNumberNotDivisibleByFloat()
    {
        $v = $this->getInstance();
        $v->validate(25, [
            'multipleOf' => 3.3
        ]);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testCheckMaximumThrowsExceptionWhenNumberIsGreaterThanMaximum()
    {
        $v = $this->getInstance();
        $v->validate(4, [
            'maximum' => 3
        ]);
    }

    public function testCheckMaximumAcceptsValuesEqualToMax()
    {
        $v = $this->getInstance();
        $x = $v->validate(3, [
            'maximum' => 3
        ]);
        $this->assertEquals(3, $x);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testCheckMaximumThrowsExceptionWhenNumberIsEqualAndExclusiveMaximumIsTrue()
    {
        $v = $this->getInstance();
        $v->validate(3, [
            'maximum' => 3,
            'exclusiveMaximum' => true
        ]);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testCheckMinimumThrowsExceptionWhenNumberIsLessThanMinimum()
    {
        $v = $this->getInstance();
        $v->validate(2, [
            'minimum' => 3
        ]);
    }

    public function testCheckMinimumAcceptsValuesEqualToMin()
    {
        $v = $this->getInstance();
        $x = $v->validate(3, [
            'minimum' => 3
        ]);
        $this->assertEquals(3, $x);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testCheckMinimumThrowsExceptionWhenNumberIsEqualAndExclusiveMinimumIsTrue()
    {
        $v = $this->getInstance();
        $v->validate(3, [
            'minimum' => 3,
            'exclusiveMinimum' => true
        ]);
    }

    public function testNumberIsNumber()
    {
        $v = $this->getInstance();
        $result = $v->validate(42.42, []);
        $this->assertEquals(42.42, $result);
    }

}