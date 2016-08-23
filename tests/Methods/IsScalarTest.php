<?php
class IsScalarTest extends DtoTest\TestCase
{
    public function testBasicPHPrealityCheck()
    {
        $dto = new \Dto\Dto();
        $obj = new stdClass();
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
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('isScalarType');
        $method->setAccessible(true);
    
        $this->assertFalse($method->invokeArgs($dto, ['dto']));
        $this->assertFalse($method->invokeArgs($dto, ['array']));
        $this->assertFalse($method->invokeArgs($dto, ['hash']));
    
        $this->assertTrue($method->invokeArgs($dto, ['integer']));
        $this->assertTrue($method->invokeArgs($dto, ['float']));
        $this->assertTrue($method->invokeArgs($dto, ['scalar']));
        $this->assertTrue($method->invokeArgs($dto, ['string']));
        $this->assertTrue($method->invokeArgs($dto, ['boolean']));
    }
}