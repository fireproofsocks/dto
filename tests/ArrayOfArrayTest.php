<?php
class ArrayOfArrayTest extends PHPUnit_Framework_Testcase
{
    
    /**
     * @ expectedException Dto\Exceptions\InvalidDataTypeException
     */
//    public function testCannotWriteScalarToArrayLocation()
//    {
//        $D = new TestArrayOfArrayTestDto();
//        $D->array = 'not an array';
//    }
    
    /**
     * @expectedException Dto\Exceptions\InvalidDataTypeException
     */
    public function testThatYouCannotWriteScalarValuestoArrayLocations()
    {
        //$D = new TestArrayOfArrayTestDto();
        $D = new TestArrayOfArrayTestDto([[1,2,3]]);
        
        //$D[] = [1,2,3];
        //$D->array[] = ['x', 'y', 'z'];
        //$D->array[] = ['a' => 'apple']; // TODO: should arrays filter out hashes?
    }
    
    public function testThatYouCanWriteArraysToArrayLocations()
    {
        $a = ['ape', 'apple', 'africa'];
        $b = ['bear', 'bun', 'boise'];
        $c = ['cat', 'chunk', 'chile'];
        $D = new TestArrayOfArrayTestDto([[$a,$b,$c]]);
    }
}

class TestArrayOfArrayTestDto extends \Dto\Dto
{
    protected $template = [
    ];
    
    protected $meta = [
        '.' => [
            'type' => 'array',
            'values' => [
                'type' => 'array'
            ]
        ]
    ];
}
