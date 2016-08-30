<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class IsScalarTest extends TestCase
{
    public function testBasicPHPrealityCheck()
    {
        $dto = new \Dto\Dto();
        $obj = new \stdClass();
        $arr = array();
        $int = 0;
        $float = 1.1;
        $str = 'string';
        $boolean = true;
        
        $this->assertFalse(is_scalar($dto));
        $this->assertFalse(is_scalar($obj));
        $this->assertFalse(is_scalar($arr));
        
        $this->assertTrue(is_scalar($int));
        $this->assertTrue(is_scalar($float));
        $this->assertTrue(is_scalar($str));
        $this->assertTrue(is_scalar($boolean));
    
    }
    
    public function testIsScalarType()
    {
        $dto = new \Dto\Dto();
        
        $this->assertFalse($this->callProtectedMethod($dto, 'isScalarType', ['dto']));
        $this->assertFalse($this->callProtectedMethod($dto, 'isScalarType', ['array']));
        $this->assertFalse($this->callProtectedMethod($dto, 'isScalarType', ['hash']));
    
        $this->assertTrue($this->callProtectedMethod($dto, 'isScalarType', ['integer']));
        $this->assertTrue($this->callProtectedMethod($dto, 'isScalarType', ['float']));
        $this->assertTrue($this->callProtectedMethod($dto, 'isScalarType', ['scalar']));
        $this->assertTrue($this->callProtectedMethod($dto, 'isScalarType', ['string']));
        $this->assertTrue($this->callProtectedMethod($dto, 'isScalarType', ['boolean']));
    }
}