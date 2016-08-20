<?php
class ArrayOfBooleanTest extends PHPUnit_Framework_Testcase
{
    
    public function testArraySet1()
    {
        $D = new TestArrayOfBooleanTestDto();
        $D->array = [true, true, false];
        $this->assertEquals([true, true, false], $D->array->toArray());
        $this->assertEquals([true, true, false], (array)$D->array);
        
    }
    
    public function testArraySet2()
    {
        $D = new TestArrayOfBooleanTestDto();
        $D->array = [1, 1, 0];
        $this->assertEquals([true, true, false], $D->array->toArray());
        $this->assertEquals([true, true, false], (array)$D->array);
        
    }
    
    public function testArrayAppend()
    {
        $D = new TestArrayOfBooleanTestDto();
        $D->array = [true, false];
        $D->array[] = true;
        
        $this->assertEquals([true, false, true], $D->array->toArray());
        $this->assertEquals([true, false, true], (array) $D->array);
        
    }
    
    public function testArrayErraticIndex()
    {
        $D = new TestArrayOfBooleanTestDto();
        
        $erratic_index = [
            'a' => true,
            'b' => false,
            'c' => true
        ];
        
        $D->array = $erratic_index;
        $this->assertEquals([true, false, true], $D->array->toArray());
        $this->assertEquals([true, false, true], (array) $D->array);
    }

}

class TestArrayOfBooleanTestDto extends \Dto\Dto
{
    protected $template = [
        'array' => [],
    ];
    
    protected $meta = [
        'array' => [
            'type' => 'array',
            'values' => [
                'type' => 'boolean'
            ]
        ],
    ];
}