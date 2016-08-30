<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class IsValidValueTest extends TestCase
{
    public function testValid()
    {
        $value = $this->callProtectedMethod(new isValidValueTestDto(), 'isValidValue', [null]);
        $this->assertTrue($value);
        
        $value = $this->callProtectedMethod(new isValidValueTestDto(), 'isValidValue', [123]);
        $this->assertTrue($value);
    
        $value = $this->callProtectedMethod(new isValidValueTestDto(), 'isValidValue', [42.5]);
        $this->assertTrue($value);
    
        $value = $this->callProtectedMethod(new isValidValueTestDto(), 'isValidValue', ['apple']);
        $this->assertTrue($value);
    
        $value = $this->callProtectedMethod(new isValidValueTestDto(), 'isValidValue', [['array']]);
        $this->assertTrue($value);
    
        $value = $this->callProtectedMethod(new isValidValueTestDto(), 'isValidValue', [['my'=>'hash']]);
        $this->assertTrue($value);
    
        $obj = new \stdClass();
        $value = $this->callProtectedMethod(new isValidValueTestDto(), 'isValidValue', [$obj]);
        $this->assertTrue($value);
    
        $dto = new \Dto\Dto();
        $value = $this->callProtectedMethod(new isValidValueTestDto(), 'isValidValue', [$dto]);
        $this->assertTrue($value);
    }
    
    public function testInvalid()
    {
        $value = $this->callProtectedMethod(new isValidValueTestDto(), 'isValidValue', [new isValidValueTestNotDto()]);
        $this->assertFalse($value);
    }
}

class isValidValueTestDto extends \Dto\Dto {
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
}

class isValidValueTestNotDto {
    
}