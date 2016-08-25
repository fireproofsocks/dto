<?php
class FilterTest extends DtoTest\TestCase
{
    public function testFilteringUsingValueMutators()
    {
        $dto = new TestFilterDto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('filter');
        $method->setAccessible(true);
        
        $value = $method->invokeArgs($dto, ['123a4', 'x']);
        $this->assertEquals(123, $value);
        
        $value = $method->invokeArgs($dto, ['456b7', 'y']);
        $this->assertEquals(true, $value);
    }
    
    public function testFilteringUsingCompositeMutators()
    {
        $dto = new TestFilterDto();
        $reflection = new ReflectionClass(get_class($dto));
        $method = $reflection->getMethod('filter');
        $method->setAccessible(true);
    
        $hash = ['x' => '12a', 'y' => '13.1'];
        
        $value = $method->invokeArgs($dto, [$hash, '.']);
        //print_r($value); exit;
        $this->assertEquals(['x'=>12, 'y'=>true], $value->toArray());
        
    }
}

class TestFilterDto extends \Dto\Dto {
    protected $template = [
        'x' => 1,
        'y' => false,
    ];
    
    // Manually normalize this because we're not running the normalizeMeta function in our constructor
    protected $meta = [
        '.x' => [
            'type' => 'integer',
        ],
        '.y' => [
            'type' => 'boolean'
        ],
        '.' => [
            'type' => 'hash',
            'values' => ['type' => 'integer']
        ]
    ];
    
    public function __construct()
    {
        // Overriding this so we don't trigger the call to filter present in the Dto constructor.
    }
}