<?php
class ArrayOfIntegersTest extends PHPUnit_Framework_Testcase
{
    
    public function testArraySet1()
    {
        $D = new TestArrayOfIntegersTestDto();
        $D->array = [1, 2, 3];
        $this->assertEquals([1, 2, 3], $D->array->toArray());
        $this->assertEquals([1, 2, 3], (array)$D->array);
        
    }
    
    public function testArraySet2()
    {
        $D = new TestArrayOfIntegersTestDto();
        $D->array = ['1a', '2b', 3.3];
        $this->assertEquals([1, 2, 3], $D->array->toArray());
        $this->assertEquals([1, 2, 3], (array)$D->array);
        
    }
    
    public function TestArrayOfIntegersTestDto()
    {
        $D = new TestArrayOfIntegersTestDto();
        $D->array = [1, 2];
        $D->array[] = 3;
        
        $this->assertEquals([1, 2, 3], $D->array->toArray());
        $this->assertEquals([1, 2, 3], (array) $D->array);
        
    }
    
    public function testArrayErraticIndex()
    {
        $D = new TestArrayOfIntegersTestDto();
        
        $erratic_index = [
            'a' => 1,
            'b' => 2,
            'c' => 3
        ];
        
        $D->array = $erratic_index;
        $this->assertEquals([1, 2, 3], $D->array->toArray());
        $this->assertEquals([1, 2, 3], (array) $D->array);
    }
}

class TestArrayOfIntegersTestDto extends \Dto\Dto
{
    protected $template = [
        'array' => [],
    ];

    protected $meta = [
        'array' => [
            'type' => 'array',
            'values' => 'integer'
        ],
    ];
}