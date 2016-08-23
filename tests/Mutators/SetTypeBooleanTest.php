<?php
class SetTypeBooleanTest extends DtoTest\TestCase
{
    public function testStringsConvertToBoolean()
    {
        $value = $this->callProtectedMethod(new SetTypeBooleanTestDto(), 'setTypeBoolean', ['my-string', 'x']);
        $this->assertEquals(true, $value);
        $value = $this->callProtectedMethod(new SetTypeBooleanTestDto(), 'setTypeBoolean', ['', 'x']);
        $this->assertEquals(false, $value);
    }
    
    public function testIntegersConvertToBoolean()
    {
        $value = $this->callProtectedMethod(new SetTypeBooleanTestDto(), 'setTypeBoolean', [123, 'x']);
        $this->assertEquals(true, $value);
        $value = $this->callProtectedMethod(new SetTypeBooleanTestDto(), 'setTypeBoolean', [0, 'x']);
        $this->assertEquals(false, $value);
    }
}

class SetTypeBooleanTestDto extends \Dto\Dto {
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
}