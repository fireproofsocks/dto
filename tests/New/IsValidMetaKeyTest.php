<?php
class IsValidMetaKeyTest extends PHPUnit_Framework_Testcase
{
    public function testIsValidMetaKey()
    {
        $dto = new \Dto\Dto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('isValidMetaKey');
        $method->setAccessible(true);
        
        $this->assertFalse($method->invokeArgs($dto, ['']));
        $this->assertTrue($method->invokeArgs($dto, ['.']));
        $this->assertFalse($method->invokeArgs($dto, ['cat..dog']));
        $this->assertFalse($method->invokeArgs($dto, ['..catdog']));
        $this->assertTrue($method->invokeArgs($dto, ['mother.father.dog']));
    }
}