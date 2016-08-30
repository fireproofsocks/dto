<?php
class DragnetTest extends \DtoTest\TestCase
{
    /**
     * @expectedException \Dto\Exceptions\InvalidLocationException
     */
    public function testThatOnlyValuesDefinedInTheTemplateAreKept()
    {
        $values = [
            'a' => 'ape',
            'b' => 'boy',
            'c' => 'cat',
            'd' => 'dog'
        ];
        // Raise an exception?  Or silently omit the unmapped index?
        $dto = new DragnetTestDto($values);
        // We only want to keep items a, b, c
        $this->assertEquals([
            'a' => 'ape',
            'b' => 'boy',
            'c' => 'cat',
        ], $dto->toArray());
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