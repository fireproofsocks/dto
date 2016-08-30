<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class ArrayTest extends TestCase
{
    public function testAppendToArrayAtRootForTypeArrayRootNode()
    {
        $D = new \Dto\Dto([],[],['.' => ['type' => 'array']]);
        $D->append('a');
        $D->append('b');
        $D[] = 'c';
        $this->assertEquals(['a','b','c'], $D->toArray());
        
        $D->set('.', ['e','f','g']);
        $this->assertEquals(['e','f','g'], $D->toArray());
    }
    
    public function testSetArrayAtRoot()
    {
        $D = new \Dto\Dto();
        $D->set('.', ['e','f','g']);
        $this->assertEquals(['e','f','g'], $D->toArray());
    }
    
    public function testThatArrayAtChildNodeIsAlsoDto()
    {
        $D = new DeclareTypeArrayDto();
        $this->assertTrue(isset($D['array']));
        $this->assertTrue(isset($D->array));
        $this->assertInstanceOf(\Dto\Dto::class, $D->array);
    }
    
    public function testAppendToArrayAtNode()
    {
        $D = new DeclareTypeArrayDto();
        $D->array->append('a');
        $D->array->append('b');
        $D->array[] = 'c';
        
        $this->assertEquals(['array'=>['a','b','c']], $D->toArray());
        $this->assertEquals(['a','b','c'], $D->array->toArray());
    }
    
    public function testSetArrayAtNodeUsingSetMethod()
    {
        $D = new DeclareTypeArrayDto();
        $D->array->set('.', ['a','b','c']);
        $this->assertEquals(['array'=>['a','b','c']], $D->toArray());
        $this->assertEquals(['a','b','c'], $D->array->toArray());
    }
    
    public function testSetArrayAtNodeAsValue()
    {
        $D = new DeclareTypeArrayDto();
        
        $D->array = ['x','y'];
        $this->assertEquals(['x','y'], $D->array->toArray());
        $D->array->append('z');
        $this->assertEquals(['x','y','z'], (array) $D->array);

    }
    
    
    public function testThatKeysAreStripped()
    {
        $D = new DeclareTypeArrayDto();
        
        $erratic_index = [
            'a' => 'apple',
            'b' => 'banana',
            'c' => 'cherry'
        ];
        
        $D->array = $erratic_index;
        
        $this->assertEquals(['apple','banana','cherry'], $D->array->toArray());
    }
    
}
class DeclareTypeArrayDto extends \Dto\Dto
{
    protected $template = [
        'array' => [],
    ];
    
    protected $meta = [
        'array' => [
            'type' => 'array'
        ],
    ];
}