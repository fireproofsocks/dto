<?php
class FilterNodeTest extends DtoTest\TestCase
{
    public function testFilteringUsingValueMutators()
    {
        $dto = new TestFilterDto();
        
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
        $dto = new TestFilterDto();
        $hash = ['x' => '12a', 'y' => '13.1'];
        $this->callProtectedMethod($dto, 'filterNode', [$hash, '.']);
    }
    
    public function testFilteringUsingCompositeMutators()
    {
        $dto = new TestFilterDto();
        $hash = ['x' => '12a', 'y' => '13.1'];
        $value = $this->callProtectedMethod($dto, 'filterNode', [$hash, 'z']);
        $this->assertEquals(['x'=>12, 'y'=>true], $value->toArray());
    }
}

class TestFilterDto extends \Dto\Dto {
    
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