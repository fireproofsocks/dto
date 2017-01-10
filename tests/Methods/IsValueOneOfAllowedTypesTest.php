<?php

namespace DtoTest\Methods;

use DtoTest\TestCase;

class IsValueOneOfAllowedTypesTest extends TestCase
{
    public function testString()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', 'string');

        $this->assertTrue($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', ['some string']));
        $this->assertFalse($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', [123]), 'Integer not allowed');
    }

    public function testStringNullable()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', ['string', 'null']);

        $this->assertTrue($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', ['some string']));
        $this->assertTrue($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', [null]));
    }

    public function testInteger()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', 'integer');

        $this->assertTrue($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', [123]));
        $this->assertFalse($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', ['some string']), 'String not allowed');
    }

    public function testIntegerNullable()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', ['integer', 'null']);

        $this->assertTrue($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', [123]));
        $this->assertTrue($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', [null]));
    }

    public function testNumber()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', 'number');

        $this->assertTrue($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', [123]));
        $this->assertTrue($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', [12345.123]));
        $this->assertTrue($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', [12345e123]));
        $this->assertFalse($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', ['some string']), 'String not allowed');
    }

    public function testNumberNullable()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', ['number', 'null']);

        $this->assertTrue($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', [12345.123]));
        $this->assertTrue($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', [null]));
    }

    public function testObject()
    {
        $dto = $this->getDtoInstance();
        $this->setProtectedProperty($dto, 'type', 'object');

        $this->assertTrue($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', [new \stdClass()]));
        $this->assertFalse($this->callProtectedMethod($dto, 'isValueOneOfAllowedTypes', ['some string']), 'String not allowed');
    }
}