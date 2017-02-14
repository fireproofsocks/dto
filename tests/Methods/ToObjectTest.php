<?php
namespace DtoTest\Methods;

use Dto\Dto;
use DtoTest\TestCase;

class ToObjectTest extends TestCase
{
    // TODO: should this be disallowed?
    public function testArrayToObject()
    {
        $d = new Dto(['a','b','c'], ['type'=>'array']);

        $expected = new \stdClass();
        $expected->{0} = 'a';
        $expected->{1} = 'b';
        $expected->{2} = 'c';

        $this->assertEquals($expected, $d->toObject());
    }

    public function testAssociativeArrayToObject()
    {
        $d = new Dto(['a' => 'apple', 'b' => 'boy'], ['type'=>'object']);

        $expected = new \stdClass();
        $expected->a = 'apple';
        $expected->b = 'boy';

        $this->assertEquals($expected, $d->toObject());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testExceptionThrownWhenDataIsScalar()
    {
        $d = new Dto('some string', ['type'=>'string']);
        $d->toObject();
    }
}