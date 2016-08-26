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
    
}

class __ConstructTestDto extends \Dto\Dto
{
    public $filtered = false;
    
    // Override
    protected function filterRoot($value) {
        $this->filtered = true;
        return $value;
    }
}