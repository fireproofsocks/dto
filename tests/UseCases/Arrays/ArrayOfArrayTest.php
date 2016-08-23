<?php
class ArrayOfArrayTest extends DtoTest\TestCase
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
                'type' => 'array'
            ]
        ]
    ];
}
