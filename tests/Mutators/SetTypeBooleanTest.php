<?php
class SetTypeBooleanTest extends PHPUnit_Framework_Testcase
{
    protected function getMethod($dto)
    {
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('setTypeBoolean');
        $method->setAccessible(true);
        return $method;
    }
    
    public function testStringsConvertToBoolean()
    {
        $dto = new SetTypeBooleanTestDto();
        $method = $this->getMethod($dto);
        $value = $method->invokeArgs($dto, ['my-string', 'x']);
        $this->assertEquals(true, $value);
        $value = $method->invokeArgs($dto, ['', 'x']);
        $this->assertEquals(false, $value);
    }
    
    public function testIntegersConvertToBoolean()
    {
        $dto = new SetTypeBooleanTestDto();
        $method = $this->getMethod($dto);
        $value = $method->invokeArgs($dto, [123, 'x']);
        $this->assertEquals(true, $value);
        $value = $method->invokeArgs($dto, [0, 'x']);
        $this->assertEquals(false, $value);
    }
}

class SetTypeBooleanTestDto extends \Dto\Dto {
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
}