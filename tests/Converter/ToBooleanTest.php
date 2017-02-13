<?php
namespace DtoTest\Converter;

use Dto\TypeConverter;
use DtoTest\TestCase;

class ToBooleanTest extends TestCase
{
    protected function getInstance()
    {
        return new TypeConverter();
    }

    public function testEmptyArrayIsFalse()
    {
        $t = $this->getInstance();
        $this->assertEquals(false, $t->toBoolean([]));
    }

    public function testFullArrayIsTrue()
    {
        $t = $this->getInstance();
        $this->assertTrue($t->toBoolean(['not', 'empty']));
    }

    public function testObjectIsFalseWhenEmpty()
    {
        $t = $this->getInstance();
        $this->assertFalse($t->toBoolean(new \stdClass()));
    }

    public function testObjectIsTrueWhenNotEmpty()
    {
        $t = $this->getInstance();
        $obj = new \stdClass();
        $obj->a = 'apple';
        $this->assertTrue($t->toBoolean($obj));
    }

    public function testStringsAreTrueWhenNotEmpty()
    {
        $t = $this->getInstance();
        $this->assertTrue($t->toBoolean('Some string'));
    }

    public function testStringsAreFalseWhenEmpty()
    {
        $t = $this->getInstance();
        $this->assertFalse($t->toBoolean(''));
    }

    public function testZeroIsFalse()
    {
        $t = $this->getInstance();
        $this->assertFalse($t->toBoolean(0));
    }

    public function testNonZeroIntegersAreTrue()
    {
        $t = $this->getInstance();
        $this->assertTrue($t->toBoolean(1));
    }
}