<?php
namespace DtoTest\Validators\Types;

use Dto\ServiceContainer;
use Dto\Validators\Types\StringValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class StringValidatorTest extends TestCase
{
    protected function getInstance()
    {
        return new StringValidator(new ServiceContainer());
    }

    public function testInstantiation()
    {
        $v = $this->getInstance();
        $this->assertInstanceOf(ValidatorInterface::class, $v);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testWrongDataTypeThrowsException()
    {
        $v = $this->getInstance();
        $v->validate(['not', 'a', 'string'], []);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testMaxLengthThrowsExceptionWhenStringIsLongerThanMax()
    {
        $v = $this->getInstance();
        $v->validate('abcd', [
            'maxLength' => 3
        ]);
    }

    public function testMaxLengthIsValidWhenStringLengthEqualsMax()
    {
        $v = $this->getInstance();
        $result = $v->validate('abcd', [
            'maxLength' => 4
        ]);
        $this->assertEquals($result, 'abcd');
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testMinLengthThrowsExceptionWhenStringIsShorterThanMin()
    {
        $v = $this->getInstance();
        $v->validate('abc', [
            'minLength' => 4
        ]);
    }

    public function testMinLengthIsValidWhenStringLengthEqualsMin()
    {
        $v = $this->getInstance();
        $result = $v->validate('abcd', [
            'minLength' => 4
        ]);
        $this->assertEquals($result, 'abcd');
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testPatternThrowsException1()
    {
        $v = $this->getInstance();
        $result = $v->validate('foo4', [
            'pattern' => '^[a-z]$'
        ]);
    }


    public function testPatternPasses1()
    {
        $v = $this->getInstance();
        $result = $v->validate('555-1212', [
            'pattern' => '(\\([0-9]{3}\\))?[0-9]{3}-[0-9]{4}$'
        ]);
        $this->assertEquals('555-1212', $result);
    }


    public function testPatternPasses2()
    {
        $v = $this->getInstance();
        $result = $v->validate('(888)555-1212', [
            'pattern' => '(\\([0-9]{3}\\))?[0-9]{3}-[0-9]{4}$'
        ]);
        $this->assertEquals('(888)555-1212', $result);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testPatternThrowsException2()
    {
        $v = $this->getInstance();
        $result = $v->validate('(888)555-1212 ext. 532', [
            'pattern' => '(\\([0-9]{3}\\))?[0-9]{3}-[0-9]{4}$'
        ]);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidScalarValueException
     */
    public function testPatternThrowsException3()
    {
        $v = $this->getInstance();
        $result = $v->validate('(800)FLOWERS', [
            'pattern' => '(\\([0-9]{3}\\))?[0-9]{3}-[0-9]{4}$'
        ]);
    }
}