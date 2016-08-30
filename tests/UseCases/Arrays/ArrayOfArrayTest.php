<?php

namespace DtoTest\DeclareTypes\Arrays;

use DtoTest\TestCase;

class ArrayOfArrayTest extends TestCase
{
    
    /**
     * @ expectedException \Dto\Exceptions\InvalidDataTypeException
     */
//    public function testCannotWriteScalarToArrayLocation()
//    {
//        $D = new TestArrayOfArrayTestDto();
//        $D->array = 'not an array';
//    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testYouCannotWriteScalarValuesToArrayLocations1()
    {
        
        // $D = new TestArrayOfArrayTestDto([1,2,3]);
        $D = new \Dto\Dto([], ['my_array'=>[]],['my_array'=>['type'=>'array']]);
        $D->my_array = 'scalar';
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testYouCannotWriteScalarValuesToArrayLocations2()
    {
        
        // $D = new TestArrayOfArrayTestDto([1,2,3]);
        $D = new \Dto\Dto([], ['my_array'=>[]],['my_array'=>['type'=>'array']]);
        $D->set('my_array', 'scalar');
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testYouCannotWriteScalarValuesToArrayLocations3()
    {
        $D = new TestArrayOfArrayTestDto(['my_array' => 'scalar'], ['my_array'=>[]],['my_array'=>['type'=>'array']]);
        
    }
    
    
    public function testYouCanWriteArraysToArrayLocations()
    {
        $a = ['ape', 'apple', 'africa'];
        $b = ['bear', 'bun', 'boise'];
        $c = ['cat', 'chunk', 'chile'];
        $D = new TestArrayOfArrayTestDto([$a,$b,$c]);
        //print_r($D->toArray()); exit;
        $this->assertEquals([$a, $b, $c], $D->toArray());
    }
}

class TestArrayOfArrayTestDto extends \Dto\Dto
{
    protected $template = [];
    
    protected $meta = [
        '.' => [
            'type' => 'array',
            'values' => [
                'type' => 'array',
                'values' => [
                    'type' => 'scalar'
                ]
            ]
        ]
    ];
    
}
