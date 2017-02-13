<?php
namespace DtoTest\Converter;

use Dto\TypeConverter;
use DtoTest\TestCase;

class ToNumberTest extends TestCase
{
    protected function getInstance()
    {
        return new TypeConverter();
    }

    public function testStringsTurnToFloats()
    {
        $t = $this->getInstance();
        $this->assertEquals(42.1, $t->toNumber('42.1'));
    }
}