<?php

namespace DtoTest\DeclareTypes;

use Dto\DtoStrict;
use DtoTest\TestCase;

class DragnetTest extends TestCase
{
    /**
     * No exception thrown for the non-strict version
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
    public function testThatOnlyValuesDefinedInTheTemplateAreAllowed()
    {
        $values = [
            'a' => 'ape',
            'b' => 'boy',
            'c' => 'cat',
            'd' => 'dog'
        ];
        // Raise an exception?  Or silently omit the unmapped index?
        $dto = new DtoStrict($values, [
            'a' => '',
            'b' => '',
            'c' => ''
        ]);
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
    public function testWritingToInvalidLocationsRaisesExceptionsInStrictMode()
    {
        $dto = new DtoStrict([], [
            'a' => '',
            'b' => '',
            'c' => ''
        ]);
        $dto->e = 'elf';
    }
    
    public function testInvalidLocationsCannotBeWrittenTo()
    {
        $dto = new DragnetTestDto();
        $dto->e = 'elf';
        $this->assertFalse(isset($dto->e));
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