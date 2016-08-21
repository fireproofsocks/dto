<?php
class ArrayOfBooleanTest extends PHPUnit_Framework_Testcase
{
    
    public function testSettingOfArrayNodeWithActualBooleans()
    {
        $D = new TestArrayOfBooleanTestDto();
        $D->array = [true, true, false];
        $this->assertEquals([true, true, false], $D->array->toArray());
        $this->assertEquals([true, true, false], (array)$D->array);
        
    }
    
    public function testSettingOfArrayNodeWithValuesThatShouldBeConvertedToBooleans()
    {
        $D = new TestArrayOfBooleanTestDto();
        $D->array = [1, 1, 0];
        $this->assertEquals([true, true, false], $D->array->toArray());
        $this->assertEquals([true, true, false], (array)$D->array);
        
    }
    
    public function testAppendingBooleanOntoArrayNode()
    {
        $D = new TestArrayOfBooleanTestDto();
        $D->array = [true, false];
        $D->array[] = true;
        
        $this->assertEquals([true, false, true], $D->array->toArray());
        $this->assertEquals([true, false, true], (array) $D->array);
        
    }
    
    public function testThatHashWithBooleanValuesIsConvertedIntoArray()
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
    
    public function testPassingInputToConstructor()
    {
        $D = new TestArrayOfBooleanTestDto2([true, false, true]);
        $this->assertEquals([true, false, true], $D->toArray());
        $this->assertEquals([true, false, true], (array) $D);
    }
    
    public function testSettingRootNode()
    {
        $D = new TestArrayOfBooleanTestDto2();
        $D->set('.', [true, false, true]);
        $this->assertEquals([true, false, true], $D->toArray());
        $this->assertEquals([true, false, true], (array) $D);
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

class TestArrayOfBooleanTestDto2 extends \Dto\Dto
{
    protected $template = [];
    
    protected $meta = [
        '.' => [
            //'type' => 'array',
            'values' => [
                'type' => 'boolean'
            ]
        ],
    ];
}