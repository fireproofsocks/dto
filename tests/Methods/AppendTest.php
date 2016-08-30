<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class AppendTest extends TestCase
{
    /**
     * @expectedException \Dto\Exceptions\AppendException
     */
    public function testCannotAppendToRegularDto()
    {
        $D = new \Dto\Dto();
        $D->append('a');
        $D->append('b');
        $D[] = 'c';
    
    }
    
    public function testCanAppendToArrayDto()
    {
        $D = new AppendArrayTestDto();
        $D->append('a');
        $D->append('b');
        $D[] = 'c';
    
        $this->assertEquals(['a','b','c'], $D->toArray());
    }
    
    /**
     * @expectedException \Dto\Exceptions\AppendException
     */
    public function testCannotAppendToUnknownDto()
    {
        $D = new AppendUnknownTestDto();
        $D->append('a');
    }
    
    /**
     * @expectedException \Dto\Exceptions\AppendException
     */
    public function testCannotAppendToHashDto1()
    {
        $D = new AppendHashTestDto();
        $D->append('a');
    }
    
    /**
     * @expectedException \Dto\Exceptions\AppendException
     */
    public function testCannotAppendToHashDto2()
    {
        $D = new AppendHashTestDto();
        $D[] = 'a';
    }
}

class AppendArrayTestDto extends \Dto\Dto
{
    protected $meta = [
        '.' => [
            'type' => 'array'
        ]
    ];
}

class AppendUnknownTestDto extends \Dto\Dto
{
    protected $meta = [
        '.' => [
            'type' => 'unknown'
        ]
    ];
}

class AppendHashTestDto extends \Dto\Dto
{
    protected $meta = [
        '.' => [
            'type' => 'hash'
        ]
    ];
}