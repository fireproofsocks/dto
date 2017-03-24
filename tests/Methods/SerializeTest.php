<?php
namespace DtoTest\Methods;

use Dto\Dto;
use DtoTest\TestCase;

class SerializeTest extends TestCase
{
    public function testArrayToJson()
    {
        $d = new Dto(['a','b','c'], ['type'=>'array']);
        $this->assertEquals('["a","b","c"]', $d->serialize());
    }

    public function testScalarDataIsOk()
    {
        $d = new Dto('some string', ['type'=>'string']);
        $actual = $d->serialize();
        $this->assertEquals('"some string"', $actual);
    }

    public function testEmptyObjectRepresentedAsBraces()
    {
        $d = new Dto([], ['type' => 'object']);
        $this->assertEquals('{}', $d->serialize());
    }

    public function testEmptyArrayRepresentedAsSquareBrackets()
    {
        $d = new Dto([], ['type' => 'array']);
        $this->assertEquals('[]', $d->serialize());
    }
}