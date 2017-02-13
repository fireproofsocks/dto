<?php
namespace DtoTest\Converter;

use Dto\TypeConverter;
use DtoTest\TestCase;

class ToIntegerTest extends TestCase
{
    protected function getInstance()
    {
        return new TypeConverter();
    }

    public function testStringsAreConvertedToIntegers()
    {
        $t = $this->getInstance();
        $this->assertEquals(42, $t->toInteger('42'));
    }
    
    public function testArraysTurnToZero()
    {
        $t = $this->getInstance();
        $this->assertEquals(0, $t->toInteger(['a','b']));
    }
}