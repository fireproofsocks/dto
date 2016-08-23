<?php
class SetTypeScalarTest extends DtoTest\TestCase
{
    public function testIntegersConvertToStrings()
    {
        $value = $this->callProtectedMethod(new SetTypeScalarTestDto(), 'setTypeScalar', [123, 'ignoreme']);
        $this->assertEquals('123', $value);
    }
    
    public function testBooleanConvertToStrings()
    {
        $value = $this->callProtectedMethod(new SetTypeScalarTestDto(), 'setTypeScalar', [true, 'ignoreme']);
        $this->assertEquals('1', $value);
    
        $value = $this->callProtectedMethod(new SetTypeScalarTestDto(), 'setTypeScalar', [false, 'ignoreme']);
        $this->assertEquals('', $value);
    }
    
    public function testNullConvertsToEmptyString()
    {
        $value = $this->callProtectedMethod(new SetTypeScalarTestDto(), 'setTypeScalar', [null, 'ignoreme']);
        $this->assertEquals('', $value);
    }
}

class SetTypeScalarTestDto extends \Dto\Dto {
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
}