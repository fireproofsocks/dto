<?php
class GetFunctionNameTest extends PHPUnit_Framework_Testcase
{
    public function testSimpleFunctionName()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getFunctionName');
        $method->setAccessible(true);
        
        $value = $method->invokeArgs($dto, ['set', 'something']);
        $this->assertEquals('setSomething', $value);
    }
    
    public function testFunctionNameForDeepIndexWithDots()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getFunctionName');
        $method->setAccessible(true);
    
        $value = $method->invokeArgs($dto, ['set', 'something.really.cool']);
        $this->assertEquals('setSomethingReallyCool', $value);
    }
    
    public function testFunctionNameForDeepIndexWithDotsWithCapitalLetters()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getFunctionName');
        $method->setAccessible(true);
        
        $value = $method->invokeArgs($dto, ['set', 'SOMETHING.REALLY.COOL']);
        $this->assertEquals('setSomethingReallyCool', $value);
    }
    
    public function testReturnsFalseWhenInputInvalid()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getFunctionName');
        $method->setAccessible(true);
    
        $value = $method->invokeArgs($dto, ['', 'SOMETHING.REALLY.COOL']);
        $this->assertFalse($value);
        $value = $method->invokeArgs($dto, ['set', '']);
        $this->assertFalse($value);
    }
    
    public function testThatItIsOkToPassFalseToMethodExists()
    {
        $this->assertFalse(method_exists($this, false));
    }
}