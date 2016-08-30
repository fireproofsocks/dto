<?php

namespace DtoTest\DeclareTypes\Arrays;

use DtoTest\TestCase;

class ArrayOfFloatTest extends TestCase
{
    
    public function testArraySet1()
    {
        $D = new TestArrayOfFloatTestDto();
        $D->array = [1.1, 2.2, 3.3];
        $this->assertEquals([1.1, 2.2, 3.3], $D->array->toArray());
        $this->assertEquals([1.1, 2.2, 3.3], (array)$D->array);
        
    }
    
    
    public function testArrayAppend()
    {
        $D = new TestArrayOfFloatTestDto();
        $D->array = [1.1, 2.2];
        $D->array[] = 3.3;
        
        $this->assertEquals([1.1, 2.2, 3.3], $D->array->toArray());
        $this->assertEquals([1.1, 2.2, 3.3], (array) $D->array);
        
    }
    
    public function testArrayErraticIndex()
    {
        $D = new TestArrayOfFloatTestDto();
        
        $erratic_index = [
            'a' => 1.1,
            'b' => 2.2,
            'c' => 3.3
        ];
        
        $D->array = $erratic_index;
        $this->assertEquals([1.1, 2.2, 3.3], $D->array->toArray());
        $this->assertEquals([1.1, 2.2, 3.3], (array) $D->array);
    }
    
}

class TestArrayOfFloatTestDto extends \Dto\Dto
{
    protected $template = [
        'array' => [],
    ];
    
    protected $meta = [
        'array' => [
            'type' => 'array',
            'values' => [
                'type' => 'float'
            ]
        ]
    ];
}