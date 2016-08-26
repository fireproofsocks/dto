<?php
class __ConstructTest extends DtoTest\TestCase
{
    public function testInstantiation()
    {
        $dto = new __ConstructTestDto();
        $this->assertInstanceOf('__ConstructTestDto', $dto);
    }
    
    public function test()
    {
        $hash = ['x' => '12a', 'y' => '13.1'];
        $dto = new __ConstructTestDto($hash);
        $this->assertEquals($hash, $dto->toArray());
        $this->assertTrue($dto->filtered);
    }
    
    // testBypass
    public function testBypass()
    {
        $hash = ['x' => '12a', 'y' => '13.1'];
        $dto = new __ConstructTestDto($hash, [], [], true);
        $this->assertEquals($hash, $dto->toArray());
        $this->assertFalse($dto->filtered);
    }
}

class __ConstructTestDto extends \Dto\Dto
{
    public $filtered = false;
    
    // Override
    protected function filter($value, $index, $bypass = false) {
        $this->filtered = true;
        return $value;
    }
}