<?php
namespace DtoTest\Methods;

use Dto\Dto;
use DtoTest\TestCase;

class GetTest extends TestCase
{
    public function testGet()
    {
        $d = new Dto(null, ['type'=>'object']);
        $d->a = 'apple';
        $this->assertEquals('apple', $d->get('a'));
    }

    public function testGetRetrievesDefaultObjectValue()
    {
        $d = new Dto(null, ['type' => 'object', 'default' => ['a' => 'apple']]);
        $this->assertEquals('apple', $d->get('a'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetThrowsExceptionForInvalidKeys()
    {
        $d = new Dto();
        $d->get(['not', 'a', 'valid', 'key']);
    }

    public function testGetRetrievesDefaultArrayValue()
    {
        $d = new Dto(null, ['type' => 'array', 'default' => ['apple']]);
        $this->assertEquals('apple', $d->get(0));
    }

}