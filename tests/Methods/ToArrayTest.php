<?php
namespace DtoTest\Methods;

use Dto\Dto;
use DtoTest\TestCase;

class ToArrayTest extends TestCase
{
    public function testArrayToArray()
    {
        $d = new Dto(['a','b','c'], ['type'=>'array']);
        $this->assertEquals(['a','b','c'], $d->toArray());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testExceptionThrownWhenDataIsScalar()
    {
        $d = new Dto('some string', ['type'=>'string']);
        $d->toArray();
    }
}