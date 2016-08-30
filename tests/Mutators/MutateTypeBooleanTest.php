<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class MutateTypeBooleanTest extends TestCase
{
    public function testStringsConvertToBoolean()
    {
        $value = $this->callProtectedMethod(new MutateTypeBooleanTestDto(), 'mutateTypeBoolean', ['my-string', 'x']);
        $this->assertEquals(true, $value);
        $value = $this->callProtectedMethod(new MutateTypeBooleanTestDto(), 'mutateTypeBoolean', ['', 'x']);
        $this->assertEquals(false, $value);
    }
    
    public function testIntegersConvertToBoolean()
    {
        $value = $this->callProtectedMethod(new MutateTypeBooleanTestDto(), 'mutateTypeBoolean', [123, 'x']);
        $this->assertEquals(true, $value);
        $value = $this->callProtectedMethod(new MutateTypeBooleanTestDto(), 'mutateTypeBoolean', [0, 'x']);
        $this->assertEquals(false, $value);
    }
}

class MutateTypeBooleanTestDto extends \Dto\Dto {
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
}