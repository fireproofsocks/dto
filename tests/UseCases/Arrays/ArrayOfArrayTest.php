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
    public function testYouCannotWriteScalarValuesToArrayLocations()
    {
        //$D = new TestArrayOfArrayTestDto();
        $D = new TestArrayOfArrayTestDto([1,2,3]);
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
