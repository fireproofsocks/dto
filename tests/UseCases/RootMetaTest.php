<?php

namespace DtoTest\UseCases;

use Dto\Dto;
use Dto\DtoStrict;
use DtoTest\TestCase;

class RootMetaTest extends TestCase
{
    public function testPropertiesCanBeSetOnAmbigousParents()
    {
        $dto = new RootMetaTestDto();
        $dto->y = true;
        $this->assertTrue($dto->y);
    }
    
    public function testDefinedFieldsUseTheirOwnTypeFiltering()
    {
        $dto = new RootMetaTestDto();
        $dto->x = 'A String Should Convert to an integer for an integer field';
        $this->assertEquals(0, $dto->x);
    }

    public function testNonDefinedFieldsUseTypeFilteringFromTheirParent()
    {
        $dto = new RootMetaTestDto();
        $dto->y = 1;
        $this->assertTrue($dto->y);
    }
    
    public function testRootTypeArrayStripsArrayKeysWhenValuesPassedToConstructor()
    {
        $array = [
            'a' => 'apple',
            'b' => 'ball',
            'c' => 'cat'
        ];
        $dto = new RootMetaTestDto2($array);
        
        $this->assertEquals(array_values($array), $dto->toArray());
        
        $dto = new RootMetaTestDto2();
        $dto->set('.', $array);
    
        $this->assertEquals(array_values($array), $dto->toArray());
        $dto = new RootMetaTestDto2();
        $dto[] = 'apple';
        $dto[] = 'ball';
        $dto[] = 'cat';
        
        $this->assertEquals(array_values($array), $dto->toArray());
    }
    
    public function testRootTypeArrayStripsArrayKeysWhenValuesSetViaSetMethod()
    {
        $array = [
            'a' => 'apple',
            'b' => 'ball',
            'c' => 'cat'
        ];
        
        $dto = new RootMetaTestDto2();
        $dto->set('.', $array);
        
        $this->assertEquals(array_values($array), $dto->toArray());
        
    }
    
    public function testRootTypeArrayStripsArrayKeysWhenValuesAppendedUsingSquareBracketNotation()
    {
        $array = [
            'a' => 'apple',
            'b' => 'ball',
            'c' => 'cat'
        ];
        
        $dto = new RootMetaTestDto2();
        $dto[] = 'apple';
        $dto[] = 'ball';
        $dto[] = 'cat';
        
        $this->assertEquals(array_values($array), $dto->toArray());
    }
    
    public function testRootTypeArrayStripsArrayKeysWhenValuesAppendedUsingAppendMethod()
    {
        $array = [
            'a' => 'apple',
            'b' => 'ball',
            'c' => 'cat'
        ];
        
        $dto = new RootMetaTestDto2();
        $dto->append('apple');
        $dto->append('ball');
        $dto->append('cat');
        
        $this->assertEquals(array_values($array), $dto->toArray());
    }
    
    
    public function testRootTypeArrayDoesNotAllowSettingNonDefinedLocations()
    {
        $array = [
            'a' => 'apple',
            'b' => 'ball',
            'c' => 'cat'
        ];
        
        $dto = new RootMetaTestDto2($array);
        
        $dto->{1} = 'something';  // this works
        
        $this->assertEquals(['apple','something','cat'], $dto->toArray());
        
        $dto[0] = 'amazing';
        
        $this->assertEquals(['amazing','something','cat'], $dto->toArray());
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidLocationException
     */
    public function testExceptionThrownWhenNonExistentArrayLocationsAreSet()
    {
        $array = [
            'a' => 'apple',
            'b' => 'ball',
            'c' => 'cat'
        ];
    
        $dto = new RootMetaTestDto2($array);
        $dto[3] = 'invalid location';
    }
    
}

class RootMetaTestDto extends DtoStrict
{
    protected $template = [
        'x' => 1
    ];
    
    protected $meta = [
        '.' => [
            'type' => 'hash',
            'ambiguous' => true,
            'values' => [
                'type' => 'boolean'
            ]
        ],
        '.x' => [
            'type' => 'integer'
        ]
    ];
}

class RootMetaTestDto2 extends \Dto\Dto
{
    protected $template = [];
    
    protected $meta = [
        '.' => [
            'type' => 'array',
            'values' => ['type' => 'string'],
        ]
    ];
}