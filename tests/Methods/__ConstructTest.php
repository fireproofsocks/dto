<?php

class __ConstructTest extends DtoTest\TestCase
{
    public function testInstantiation()
    {
        $dto = new __ConstructTestDto();
        $this->assertInstanceOf('__ConstructTestDto', $dto);
    }
    
    public function testSetValuesThruFilterRootViaConstructor()
    {
        $hash = ['x' => '12a', 'y' => '13.1'];
        $dto = new __ConstructTestDto($hash);
        $this->assertEquals($hash, $dto->toArray());
        $this->assertTrue($dto->filtered);
    }
    
    public function testHydrationDuringInstantiation()
    {
        $dto = new __ConstructTestRecordDto();
        $this->assertEquals(['a' => '', 'b' => 0, 'c' => false], $dto->toArray());
    }
    
    public function testNonNullableChildDtosAreHydrated()
    {
        $dto = new __ConstructTestParentDto();
        
        $this->assertNull($dto->nullable_child);
        $this->assertNotNull($dto->not_nullable_child);
        
        $this->assertEquals([
            'x' => '',
            'y' => '',
            'not_nullable_child' => [
                'a' => '',
                'b' => 0,
                'c' => false
            ],
            'nullable_child' => null,],
            $dto->toArray());
    }
}

class __ConstructTestDto extends \Dto\Dto
{
    public $filtered = false;
    
    // Override
    protected function filterRoot($value)
    {
        $this->filtered = true;
        
        return $value;
    }
}

class __ConstructTestRecordDto extends \Dto\Dto
{
    protected $template = [
        'a' => '',
        'b' => 0,
        'c' => false
    ];
}

class __ConstructTestParentDto extends \Dto\Dto
{
    protected $template = [
        'x' => '',
        'y' => '',
        'not_nullable_child' => null,
        'nullable_child' => null,
    ];
    
    protected $meta = [
        'not_nullable_child' => [
            'type' => 'dto',
            'class' => '__ConstructTestRecordDto',
        ],
        'nullable_child' => [
            'type' => 'dto',
            'class' => '__ConstructTestRecordDto',
            'nullable' => true
        ]
    ];
}