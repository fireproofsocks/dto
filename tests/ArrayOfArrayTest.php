<?php
class ArrayOfArrayTest extends PHPUnit_Framework_Testcase
{
    
    public function testArray1()
    {
        $D = new TestArrayOfArrayTestDto();
        $D->array = ['x','y','z'];
        $this->assertEquals(['x','y','z'], $D->array->toArray());
        $this->assertEquals(['x','y','z'], (array) $D->array);
        
        $D->array = ['x','y'];
        $D->array[] = 'z';
        $this->assertEquals(['x','y','z'], $D->array->toArray());
        $this->assertEquals(['x','y','z'], (array) $D->array);
        
        $erratic_index = [
            'a' => 'apple',
            'b' => 'banana',
            'c' => 'cherry'
        ];
        
        $D->array = $erratic_index;
        $this->assertEquals(['apple','banana','cherry'], $D->array->toArray());
        $this->assertEquals(['apple','banana','cherry'], (array) $D->array);
    }
    
    
    
    public function testArrayOfArray()
    {
        $D = new TestArrayOfArrayTestDto();
        $D->array = [['a', 'b', 'c'], ['a' => 'a', 'b' => 'b', 'c' => 'c']];
        $this->assertEquals([['a', 'b', 'c'], ['a', 'b', 'c']], $D->array->toArray());
    }
}

class TestArrayOfArrayTestDto extends \Dto\Dto
{
    protected $template = [
        'array' => [],
    ];
    
    protected $meta = [
        'array' => [
            'type' => 'array',
            'values' => 'array'
        ]
    ];
}
