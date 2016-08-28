<?php
class MutateTypeIntegerTest extends \DtoTest\TestCase
{
    public function testNullableLocationReturnsNullWhenSetToNull()
    {
        $value = $this->callProtectedMethod(
            new \Dto\Dto([], ['x' => null], ['x' => ['type' => 'integer', 'nullable' => true]]), 'mutateTypeInteger',
            [null, 'x']
        );
        $this->assertNull($value);
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