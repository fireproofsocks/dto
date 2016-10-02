<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class MutateTypeIntegerTest extends TestCase
{
    public function testNullIsTypeCastToInteger()
    {
        $value = $this->callProtectedMethod(
            new \Dto\Dto([], ['x' => null], ['x' => ['type' => 'integer', 'nullable' => true]]), 'mutateTypeInteger',
            [null, 'x']
        );
        $this->assertNotNull($value);
    }
    
    public function testNotNullableLocationDoesNotReturnNullWhenSetToNull()
    {
        $value = $this->callProtectedMethod(
            new \Dto\Dto([], ['x' => null], ['x' => ['type' => 'integer', 'nullable' => false]]), 'mutateTypeInteger',
            [null, 'x']
        );
        $this->assertNotNull($value);
        $this->assertEquals(0, $value);
    }
}