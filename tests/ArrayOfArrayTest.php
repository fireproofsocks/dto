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
    
    public function testArrayOfString()
    {
        $D = new TestArrayTestDto();
//        $D->array = ['a','b','c'];
//        $this->assertEquals(['a','b','c'], $D->array);
    }
    
    public function testArrayOfInteger()
    {
        $D = new TestArrayTestDto();
        $D->array_integer = ['1', '2a3', '3something'];
        $this->assertEquals([1, 2, 3], $D->array_integer->toArray());
    }
    
    
    public function testArrayOfFloat()
    {
        $D = new TestArrayTestDto();
        $D->array_float = ['1.1', '2.2a3', '3something'];
        $this->assertEquals([1.1, 2.2, 3], $D->array_float->toArray());
    }
    
    
    public function testArrayOfBoolean()
    {
        $D = new TestArrayTestDto();
        $D->array_boolean = ['something', 1, 0];
        $this->assertEquals([true, true, false], $D->array_boolean->toArray());
    }
    
    public function testArrayOfArray()
    {
        $D = new TestArrayTestDto();
        $D->array_array = [['a', 'b', 'c'], ['a' => 'a', 'b' => 'b', 'c' => 'c']];
        $this->assertEquals([['a', 'b', 'c'], ['a', 'b', 'c']], $D->array_array->toArray());
    }
}

class TestArrayOfArrayTestDto extends \Dto\Dto
{
    protected $template = [
        'array_array' => [],
    ];
    
    protected $meta = [
        'array_array' => [
            'type' => 'array',
            'values' => 'array'
        ]
    ];
}