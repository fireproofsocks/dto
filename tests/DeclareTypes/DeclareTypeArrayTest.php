<?php
class DeclareTypeArrayTest extends DtoTest\TestCase
{
    public function testAppendToArrayAtRoot()
    {
        $D = new \Dto\Dto();
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
        exit;
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
        //print_r($D->toArray());
        print_r($D->array->toArray());
        exit;
        $D->array->set('array', ['a','b','c']);
        print_r($D->toArray()); exit;
        $this->assertEquals(['array'=>['a','b','c']], $D->toArray());
        $this->assertEquals(['a','b','c'], $D->array->toArray());
    }
    
    public function testArrayBROKE()
    {
        $D = new DeclareTypeArrayDto();
        //exit;
        //print_r($D->toArray()); exit;
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