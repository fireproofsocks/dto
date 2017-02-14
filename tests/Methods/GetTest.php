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
        $this->assertEquals('apple', $d->__get('a'));
    }

    public function testGetRetrievesDefaultObjectValue()
    {
        $d = new Dto(null, ['type' => 'object', 'default' => ['a' => 'apple']]);
        $this->assertEquals('apple', $d->get('a'));
        $this->assertEquals('apple', $d->__get('a'));
        $this->assertEquals('apple', $d->a);
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
        $this->assertEquals('apple', $d->__get(0));
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testGetThrowsExceptionForScalarDataTypes()
    {
        $d = new Dto(null, ['type' => 'string']);
        $d->get('fails');
    }

    /**
     * @expectedException \Dto\Exceptions\InvalidObjectValueException
     */
    public function testGetThrowsExceptionForNonExistantKey()
    {
        $d = new Dto(null, ['type' => 'object']);
        $d->get('x');
    }
}