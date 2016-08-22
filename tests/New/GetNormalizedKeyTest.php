<?php
class GetNormalizedKeyTest extends PHPUnit_Framework_Testcase
{
    public function testGetNormalizedKey()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('getNormalizedKey');
        $method->setAccessible(true);
        
        $this->assertEquals('.cat', $method->invokeArgs($dto, ['cat']));
        $this->assertEquals('.cat', $method->invokeArgs($dto, ['.cat']));
        $this->assertEquals('.cat', $method->invokeArgs($dto, ['.cat.']));
    }
}