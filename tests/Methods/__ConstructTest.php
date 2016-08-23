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
    }
}

class __ConstructTestDto extends \Dto\Dto
{
    // Override
    protected function filter($value, $index, $bypass = false) {
        return $value;
    }
}