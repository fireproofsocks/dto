<?php
class IsValidTargetLocationTest extends PHPUnit_Framework_Testcase
{
    public function testThatDefinedIndexIsValidTarget()
    {
        $dto = new IsValidTargetLocationTestDto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('isValidTargetLocation');
        $method->setAccessible(true);
    
        $template = [
            'my_index' => null
        ];
        
        $this->assertTrue($method->invokeArgs($dto, ['my_index', $template]));
    }
    
    public function testThatUndefinedIndexIsNotValidTarget()
    {
        $dto = new IsValidTargetLocationTestDto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('isValidTargetLocation');
        $method->setAccessible(true);
        
        $template = [
            'my_index' => null
        ];
        
        $this->assertFalse($method->invokeArgs($dto, ['does-not-exist', $template]));
    }
    
    public function testThatEmptyTemplateDoesNotRestrictTargets()
    {
        $dto = new IsValidTargetLocationTestDto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('isValidTargetLocation');
        $method->setAccessible(true);
        
        $template = [];
        
        $this->assertTrue($method->invokeArgs($dto, ['does-not-exist', $template]));
    }
    
    public function testThatTheRootLocationIsValid()
    {
        $dto = new IsValidTargetLocationTestDto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('isValidTargetLocation');
        $method->setAccessible(true);
    
        $template = [
            'my_index' => null,
        ];
        
        $this->assertTrue($method->invokeArgs($dto, ['.', $template]));
    }
    
    public function testThatNormalizedLocationsCanBeUsed()
    {
        $dto = new IsValidTargetLocationTestDto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('isValidTargetLocation');
        $method->setAccessible(true);
    
        $template = [
            'my_index' => null,
        ];
        
        $this->assertTrue($method->invokeArgs($dto, ['.my_index', $template]));
    }
}

class IsValidTargetLocationTestDto extends \Dto\Dto
{
    public function __construct()
    {
        // override
    }
}