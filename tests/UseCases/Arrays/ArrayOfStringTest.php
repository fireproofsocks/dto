<?php
class ArrayOfStringTest extends DtoTest\TestCase
{
    
    public function testArrayNodeSet()
    {
        $D = new TestArrayOfStringTestDto();
        $D->array = ['x', 'y', 'z'];
        $this->assertEquals(['x', 'y', 'z'], $D->array->toArray());
        $this->assertEquals(['x', 'y', 'z'], (array)$D->array);
    
    }
    
    public function testArrayNodeAppend()
    {
        $D = new TestArrayOfStringTestDto();
        $D->array = ['x','y'];
        $D->array[] = 'z';
        
        $this->assertEquals(['x','y','z'], $D->array->toArray());
        $this->assertEquals(['x','y','z'], (array) $D->array);

    }
    
    public function testThatKeysStrippedFromArray()
    {
        $D = new TestArrayOfStringTestDto();
        
        $erratic_index = [
            'a' => 'apple',
            'b' => 'banana',
            'c' => 'cherry'
        ];

        $D->array = $erratic_index;
        $this->assertEquals(['apple','banana','cherry'], $D->array->toArray());
        $this->assertEquals(['apple','banana','cherry'], (array) $D->array);
    }
    
}

class TestArrayOfStringTestDto extends \Dto\Dto
{
    protected $template = [
        'array' => [],
    ];
    
    protected $meta = [
        'array' => [
            'type' => 'array',
            'values' => [
                'type' => 'scalar'
            ]
        ]
    ];
}