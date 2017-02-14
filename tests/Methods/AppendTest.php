<?php
namespace DtoTest\Methods;

use Dto\Dto;
use DtoTest\TestCase;

class AppendTest extends TestCase
{
    public function testAppendToArray()
    {
        $d = new Dto(null, ['type' => 'array']);
        $d->append('a');
        $d->append('b');
        $d->append('c');

        $this->assertEquals(['a','b','c'], $d->toArray());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidArrayOperationException
     */
    public function testAppendToNullInstanceWithEmptySchema()
    {
        $d = new Dto();
        $d->append('a');
    }

    public function testAppendToArrayInstanceWithEmptySchema()
    {
        $d = new Dto([]);
        $d->append('a');
        $d->append('b');
        $d->append('c');

        $this->assertEquals(['a','b','c'], $d->toArray());
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testAppendAllowedOnlyOnArrays()
    {
        $d = new Dto(null, ['type' => 'string']);
        $d->append('this should fail');
    }
}