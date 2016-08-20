<?php
class ArrayOfDtoTest extends PHPUnit_Framework_Testcase
{
    
    public function testArraySet()
    {
        $D = new TestArrayOfDtoTestDto();
        
        $records = [];
        $records[] = new TestArrayOfDtoRecordSetDto(['x'=>'a', 'y'=>'b', 'z'=>'c']);
        $records[] = new TestArrayOfDtoRecordSetDto(['x'=>'d', 'y'=>'e', 'z'=>'f']);
        $records[] = new TestArrayOfDtoRecordSetDto(['x'=>'g', 'y'=>'h', 'z'=>'i']);
        
        $D->array = $records;
        
        
        //$this->assertEquals(['x', 'y', 'z'], $D->array->toArray());
        //$this->assertEquals(['x', 'y', 'z'], (array)$D->array);
        
    }
    
//    public function testArrayAppend()
//    {
//        $D = new TestArrayOfStringTestDto();
//        $D->array = ['x','y'];
//        $D->array[] = 'z';
//
//        $this->assertEquals(['x','y','z'], $D->array->toArray());
//        $this->assertEquals(['x','y','z'], (array) $D->array);
//
//    }
//
//    public function testArrayErraticIndex()
//    {
//        $D = new TestArrayOfStringTestDto();
//
//        $erratic_index = [
//            'a' => 'apple',
//            'b' => 'banana',
//            'c' => 'cherry'
//        ];
//
//        $D->array = $erratic_index;
//        $this->assertEquals(['apple','banana','cherry'], $D->array->toArray());
//        $this->assertEquals(['apple','banana','cherry'], (array) $D->array);
//    }
    
}

class TestArrayOfDtoTestDto extends \Dto\Dto
{
    protected $template = [
        'array' => [],
    ];
    
    protected $meta = [
        'array' => [
            'type' => 'array',
            'values' => [
                'type' => 'dto',
                'class' => 'TestArrayOfDtoRecordSetDto'
            ]
        ]
    ];
}
class TestArrayOfDtoRecordSetDto {
    protected $template = [
        'x' => '',
        'y' => '',
        'z'
    ];
}