<?php
class DragnetTest extends \DtoTest\TestCase
{
    public function testThatOnlyValuesDefinedInTheTemplateAreKept()
    {
        $values = [
            'a' => 'ape',
            'b' => 'boy',
            'c' => 'cat',
            'd' => 'dog'
        ];
        
        $dto = new DragnetTestDto($values);
        unset($values['d']);
        $this->assertEquals($values, $dto->toArray());
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidLocationException
     */
    public function testThatInvalidLocationsCannotBeWrittenTo()
    {
        $dto = new DragnetTestDto();
        $dto->e = 'elf';
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testThatArraysCannotBeWrittenToScalarLocations()
    {
        $dto = new DragnetTestDto();
        $dto->a = ['some', 'array'];
    }
}

class DragnetTestDto extends \Dto\Dto
{
    protected $template = [
        'a' => '',
        'b' => '',
        'c' => ''
    ];
}