<?php
namespace DtoTest\Validators\Types;

use Dto\ServiceContainer;
use Dto\Validators\Types\ObjectValidator;
use Dto\Validators\ValidatorInterface;
use DtoTest\TestCase;

class ObjectValidatorTest extends TestCase
{
    protected function getInstance()
    {
        return new ObjectValidator(new ServiceContainer());
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
        $v->validate('not an object', []);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidObjectValueException
     */
    public function testMaxPropertiesThrowExceptionWhenObjectHasTooManyProperties()
    {
        $v = $this->getInstance();
        $object = ['a' => 'apple','b' =>'boy', 'c' =>'cat'];
        $result = $v->validate($object, [
            'maxProperties' => 2
        ]);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidObjectValueException
     */
    public function testMinPropertiesThrowExceptionWhenObjectHasTooFewProperties()
    {
        $v = $this->getInstance();
        $object = ['a' => 'apple','b' =>'boy'];
        $v->validate($object, [
            'minProperties' => 3
        ]);
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidObjectValueException
     */
    public function testRequiredFieldsThrowExceptionWhenMissing()
    {
        $v = $this->getInstance();
        $object = ['a' => 'apple','b' =>'boy'];
        $v->validate($object, [
            'required' => ['a','b','c']
        ]);
    }
}