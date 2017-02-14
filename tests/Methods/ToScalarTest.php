<?php
namespace DtoTest\Methods;

use Dto\Dto;
use DtoTest\TestCase;

class ToScalarTest extends TestCase
{
    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testToScalarThrowsExceptionForNonScalarData()
    {
        $d = new Dto([], ['type' => 'array']);
        $d->toScalar();
    }

    public function testToScalarReturnsScalarValue()
    {
        $d = new Dto(444333, ['type' => 'integer']);
        $this->assertEquals(444333, $d->toScalar());
    }
}