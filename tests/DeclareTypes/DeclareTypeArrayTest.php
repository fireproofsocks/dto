<?php
class DeclareTypeArrayTest extends DtoTest\TestCase
{
    public function testArray1()
    {
        $D = new DeclareTypeArrayDto();
        exit;
        print_r($D->toArray()); exit;
        print "===============================================================\n";
        $D->array = ['x','y'];
//        $D->array[] = 'z';
        $this->assertEquals(['x','y'], $D->array->toArray());
        $D->array->append('z');
        
        $this->assertEquals(['x','y','z'], (array) $D->array);
        
    }
    
    public function testArray2()
    {
        $D = new \Dto\Dto([], ['array' => []], ['array' => ['type' => 'array']]);
        
        $D->array = ['x','y'];
        $D->array[] = 'z';
        
        // Both options are possible, but strictly speaking, the Dto is NOT an array
        $this->assertEquals(['x','y','z'], $D->array->toArray());
        $this->assertEquals(['x','y','z'], (array) $D->array);
        
        $D->array->append('aa');
        $this->assertEquals(['x','y','z', 'aa'], $D->array->toArray());
        $this->assertEquals(['x','y','z', 'aa'], (array) $D->array);
        
    }
    
    public function testArray3()
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