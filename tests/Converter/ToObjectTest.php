<?php
namespace DtoTest\Converter;

use Dto\TypeConverter;
use DtoTest\TestCase;

class ToObjectTest extends TestCase
{
    protected function getInstance()
    {
        return new TypeConverter();
    }

    public function testStdClassObjectIsConvertedToArray()
    {
        $t = $this->getInstance();

        $obj = new \stdClass();
        $obj->a = 'apple';
        $obj->b = 'boy';

        $this->assertEquals(['a' => 'apple', 'b' => 'boy'], $t->toObject($obj));
    }

    public function testArrayIsLeftAlone()
    {
        $t = $this->getInstance();

        $this->assertEquals(['a' => 'apple', 'b' => 'boy'], $t->toObject(['a' => 'apple', 'b' => 'boy']));
    }

    public function testOtherValuesReturnEmptyArray()
    {
        $t = $this->getInstance();

        $this->assertEquals([], $t->toObject(false));
    }
}