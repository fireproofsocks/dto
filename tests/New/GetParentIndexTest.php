<?php
class GetParentIndexTest extends PHPUnit_Framework_Testcase
{
    public function testThatDepthOfTwoResolvesToDepthOfOne()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getParentIndex');
        $method->setAccessible(true);
    
        $value = $method->invokeArgs($dto, ['.something.else']);
        $this->assertEquals('.something', $value);
    }
    
    public function testThatDepthOfThreeResolvesToDepthOfTwo()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getParentIndex');
        $method->setAccessible(true);
        
        $value = $method->invokeArgs($dto, ['.something.else.yet']);
        $this->assertEquals('.something.else', $value);
    }
    
    public function testThatRootResolvesToRoot()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getParentIndex');
        $method->setAccessible(true);
        
        $value = $method->invokeArgs($dto, ['.']);
        $this->assertEquals('.', $value);
    }
    
    public function testThatDepthOfOneResolvesToRoot()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getParentIndex');
        $method->setAccessible(true);
        
        $value = $method->invokeArgs($dto, ['.something']);
        $this->assertEquals('.', $value);
    }
}