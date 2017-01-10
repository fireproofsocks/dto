<?php

namespace DtoTest\Methods;

use DtoTest\TestCase;

class IsTrueArrayTest extends TestCase
{
    public function testAnyScalarValueIsNotTrueArray()
    {
        $dto = $this->getDtoInstance();

        $bad_values = [null, true, false, 123, 'some-string', new \stdClass()];
        foreach ($bad_values as $v) {
            $this->assertFalse($this->callProtectedMethod($dto, 'isTrueArray', [$v]));
        }
    }

    public function testAssociativeArrayIsNotTrueArray()
    {
        $dto = $this->getDtoInstance();

        $v = [
            'x' => 'xray',
            'y' => 'yellow',
            'z' => 'zebra'
        ];

        $this->assertFalse($this->callProtectedMethod($dto, 'isTrueArray', [$v]));

    }


    public function testArraysWithNonConsecutiveIntegerKeysAreNotTrueArrays()
    {
        $dto = $this->getDtoInstance();

        $v = [
            0 => 'xray',
            1 => 'yellow',
            5 => 'zebra'
        ];

        $this->assertFalse($this->callProtectedMethod($dto, 'isTrueArray', [$v]));
    }


    public function testSimpleArraysAreTrueArrays()
    {
        $dto = $this->getDtoInstance();

        $v = [
            'xray',
            'yellow',
            'zebra'
        ];

        $this->assertTrue($this->callProtectedMethod($dto, 'isTrueArray', [$v]));
    }
}