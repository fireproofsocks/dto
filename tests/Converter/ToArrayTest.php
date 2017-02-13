<?php
namespace DtoTest\Converter;

use Dto\TypeConverter;
use DtoTest\TestCase;


class ToArrayTest extends TestCase
{
    protected function getInstance()
    {
        return new TypeConverter();
    }

    public function testStdClassObjectIsConvertedToArrayWithoutKeys()
    {
        $t = $this->getInstance();

        $obj = new \stdClass();
        $obj->a = 'apple';
        $obj->b = 'boy';

        $this->assertEquals(['apple', 'boy'], $t->toArray($obj));
    }

    public function testArrayIsStrippedOfKeys()
    {
        $t = $this->getInstance();

        $this->assertEquals(['apple', 'boy'], $t->toArray(['a' => 'apple', 'b' => 'boy']));
    }

    public function testOtherValuesReturnEmptyArray()
    {
        $t = $this->getInstance();
        $this->assertEquals([], $t->toArray(false));
    }

}