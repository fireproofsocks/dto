<?php
namespace DtoTest\Converter;

use Dto\TypeConverter;
use DtoTest\TestCase;

class ToNullConverterTest extends TestCase
{
    protected function getInstance()
    {
        return new TypeConverter();
    }

    public function testNullIsNullIsNull()
    {
        $t = $this->getInstance();
        $this->assertNull($t->toNull('something'));
        $this->assertNull($t->toNull([]));
        $this->assertNull($t->toNull(new \stdClass()));
        $this->assertNull($t->toNull(0));
    }
}