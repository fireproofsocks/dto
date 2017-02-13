<?php
namespace DtoTest\Converter;

use Dto\TypeConverter;
use DtoTest\TestCase;

class ToStringTest extends TestCase
{
    protected function getInstance()
    {
        return new TypeConverter();
    }

    public function testArrayReturnsEmptyString()
    {
        $t = $this->getInstance();

        $this->assertEquals('', $t->toString([]));
    }

    public function testObjectsCallTheirToStringMethod()
    {
        $t = $this->getInstance();
        $obj = new ToStringTestObject();
        $this->assertEquals('apple', $t->toString($obj));
    }

    public function testOtherObjectsConvertToEmptyString()
    {
        $t = $this->getInstance();
        $obj = new \stdClass();
        $this->assertEquals('', $t->toString($obj));
    }

    public function testIntegersAreConvertedToStrings()
    {
        $t = $this->getInstance();
        $this->assertEquals('42', $t->toString(42));
    }
}

class ToStringTestObject
{
    public function __toString()
    {
        return 'apple';
    }
}