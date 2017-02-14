<?php
namespace DtoTest\Methods;

use Dto\Dto;
use DtoTest\TestCase;

class ToJsonTest extends TestCase
{
    public function testArrayToJson()
    {
        $d = new Dto(['a','b','c'], ['type'=>'array']);
        $this->assertEquals('["a","b","c"]', $d->toJson());
    }

    public function testArrayToPrettyJson()
    {
        $d = new Dto(['a','b','c'], ['type'=>'array']);
        $this->assertEquals(json_encode(['a','b','c'], JSON_PRETTY_PRINT), $d->toJson(true));
    }

    public function testScalarDataIsOk()
    {
        $d = new Dto('some string', ['type'=>'string']);
        $actual = $d->toJson();
        $this->assertEquals('"some string"', $actual);
    }
    
    public function testEmptyObjectRepresentedAsBraces()
    {
        $d = new Dto([], ['type' => 'object']);
        $this->assertEquals('{}', $d->toJson());
    }
    
    public function testEmptyArrayRepresentedAsSquareBrackets()
    {
        $d = new Dto([], ['type' => 'array']);
        $this->assertEquals('[]', $d->toJson());
    }
}