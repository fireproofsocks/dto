<?php

namespace DtoTest\DeclareTypes;

use Dto\Dto;
use DtoTest\TestCase;

class InstantiationTest extends TestCase
{
    // Just a little homework.
    public function testArrayUnion()
    {
        $a = [
            'a' => 'apple',
            'b' => 'boy'
        ];
        $b = [
            'b' => 'bad',
            'c' => 'cat'
        ];
        
        $this->assertEquals(['a' => 'apple', 'b' => 'bad', 'c' => 'cat'], array_replace($a, $b));
    }
    
    /**
     * Before this condition was identified, passing non-empty values to the constructor
     * would override the template entirely.
     */
    public function testInstantiationWithPropertiesNotInTemplateStillReturnsHydratedArray()
    {
        $dto = new InstantiationTestDto(['x' => '', 'b' => 3]);
        $array = $dto->toArray();
        $this->assertNotEmpty($array);
        $this->assertTrue(array_key_exists('a', $array));
        $this->assertTrue(array_key_exists('b', $array));
        $this->assertTrue(array_key_exists('c', $array));
        $this->assertFalse(array_key_exists('x', $array));
        $this->assertEquals(3, $dto->get('b'));
        
    }
}

class InstantiationTestDto extends Dto
{
    protected $template = [
        'a' => '',
        'b' => 2,
        'c' => false,
    ];
}