<?php
namespace DtoTest\Validators\Types;

use Dto\ServiceContainer;
use Dto\Validators\Types\ArrayValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class ArrayValidatorTest extends TestCase
{
    protected function getInstance()
    {
        return new ArrayValidator(new ServiceContainer());
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
        $v->validate('not an array', []);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidArrayValueException
     */
    public function testMaxItemsThrowExceptionWhenArrayIsTooLong()
    {
        $v = $this->getInstance();
        $v->validate(['a','b','c'], [
            'maxItems' => 2
        ]);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidArrayValueException
     */
    public function testMinItemsThrowExceptionWhenArrayIsTooShort()
    {
        $v = $this->getInstance();
        $v->validate(['a','b'], [
            'minItems' => 3
        ]);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidArrayValueException
     */
    public function testUniqueItemsThrowsExceptionWhenItemsAreNotUnique()
    {
        $v = $this->getInstance();
        $v->validate(['a','b','b'], [
            'uniqueItems' => true
        ]);
    }

    public function testArrayIsArray()
    {
        $v = $this->getInstance();
        $result = $v->validate(['a','b','c'], []);
        $this->assertEquals(['a','b','c'], $result);
    }
}