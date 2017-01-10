<?php

namespace DtoTest\Methods;

use DtoTest\TestCase;

class ConvertToObjectTest extends TestCase
{
    public function testConvertArrayToObject()
    {
        $dto = $this->getDtoInstance();

        $array = [
            'a' => 'apple',
            'b' => 'boy',
            'c' => 'cat'
        ];

        $result = $this->callProtectedMethod($dto, 'convertToObject', [$array]);

        $obj = new \stdClass();
        $obj->a = 'apple';
        $obj->b = 'boy';
        $obj->c = 'cat';

        $this->assertEquals($obj, $result);
    }

    public function testConvertIntegerToObject()
    {
        $dto = $this->getDtoInstance();

        $result = $this->callProtectedMethod($dto, 'convertToObject', [123]);

        $this->assertEquals(new \stdClass(), $result);
    }
}