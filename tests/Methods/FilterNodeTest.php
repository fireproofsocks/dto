<?php

namespace DtoTest\DeclareTypes;

use DtoTest\TestCase;

class FilterNodeTest extends TestCase
{
    public function testFilteringUsingValueMutators()
    {
        $dto = new FilterNodeTestDto();
        
        $value = $this->callProtectedMethod($dto, 'filterNode', ['123a4', 'x']);
        $this->assertEquals(123, $value);
        
        $value = $this->callProtectedMethod($dto, 'filterNode', ['456b7', 'y']);
        $this->assertEquals(true, $value);
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidLocationException
     */
    public function testFilteringDotNodeIsNotAllowed()
    {
        $dto = new FilterNodeTestDto();
        $hash = ['x' => '12a', 'y' => '13.1'];
        $this->callProtectedMethod($dto, 'filterNode', [$hash, '.']);
    }
    
    public function testFilteringUsingCompositeMutators()
    {
        $dto = new FilterNodeTestDto();
        $hash = ['x' => '12a', 'y' => '13.1'];
        $value = $this->callProtectedMethod($dto, 'filterNode', [$hash, 'z']);
        $this->assertEquals(['x'=>12, 'y'=>true], $value->toArray());
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidLocationException
     */
    public function testInvalidMappingThrowsException()
    {
        $dto = new FilterNodeTestDto2();
        $hash = ['x' => '12a', 'y' => '13.1'];
        $this->callProtectedMethod($dto, 'filterNode', [$hash, 'z']);
    }
    
    /**
     * @expectedException \Dto\Exceptions\InvalidDataTypeException
     */
    public function testInvalidValueThrowsException()
    {
        $dto = new FilterNodeTestDto3();
        $hash = ['x' => '12a', 'y' => '13.1'];
        $this->callProtectedMethod($dto, 'filterNode', [$hash, 'z']);
    }
}

class FilterNodeTestDto extends \Dto\Dto {
    
    protected $template = [
        'x' => 1,
        'y' => false,
        'z' => [
            'x' => 1,
            'y' => false,
        ]
    ];
    
    protected $meta = [
        '.x' => [
            'type' => 'integer',
        ],
        '.y' => [
            'type' => 'boolean'
        ],
        '.z.x' => [
            'type' => 'integer',
        ],
        '.z.y' => [
            'type' => 'boolean'
        ],
        '.' => [
            'type' => 'hash',
            'values' => ['type' => 'integer']
        ]
    ];
}

class FilterNodeTestDto2 extends FilterNodeTestDto
{
    protected function isValidMapping($value, $index)
    {
        return false;
    }
}

class FilterNodeTestDto3 extends FilterNodeTestDto
{
    protected function isValidValue($value)
    {
        return false;
    }
}