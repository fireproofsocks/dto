<?php
class SetTypeUnknownTest extends DtoTest\TestCase
{
    public function testPassthru()
    {
        $value = $this->callProtectedMethod(new SetTypeUnknownTestDto(), 'setTypeUnknown', [123, 'ignoreme']);
        $this->assertEquals(123, $value);
    
        $value = $this->callProtectedMethod(new SetTypeUnknownTestDto(), 'setTypeUnknown', [42.5, 'ignoreme']);
        $this->assertEquals(42.5, $value);
    
        $value = $this->callProtectedMethod(new SetTypeUnknownTestDto(), 'setTypeUnknown', ['apple', 'ignoreme']);
        $this->assertEquals('apple', $value);
    
        $value = $this->callProtectedMethod(new SetTypeUnknownTestDto(), 'setTypeUnknown', [['array'], 'ignoreme']);
        $this->assertEquals(['array'], $value);
    
        $value = $this->callProtectedMethod(new SetTypeUnknownTestDto(), 'setTypeUnknown', [['my'=>'hash'], 'ignoreme']);
        $this->assertEquals(['my'=>'hash'], $value);
    
        $obj = new stdClass();
        $value = $this->callProtectedMethod(new SetTypeUnknownTestDto(), 'setTypeUnknown', [$obj, 'ignoreme']);
        $this->assertEquals($obj, $value);
        
        $dto = new \Dto\Dto();
        $value = $this->callProtectedMethod(new SetTypeUnknownTestDto(), 'setTypeUnknown', [$dto, 'ignoreme']);
        $this->assertEquals($dto, $value);
    
        $obj = new SomeNonDtoClass();
        $this->callProtectedMethod(new SetTypeUnknownTestDto(), 'setTypeUnknown', [$obj, 'ignoreme']);
        $this->assertEquals($dto, $value);
    }
    
    
}

class SetTypeUnknownTestDto extends \Dto\Dto {
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
}

class SomeNonDtoClass {
    
}