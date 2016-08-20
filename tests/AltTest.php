<?php

// TODO: flip this back once ready...
use Dto\DtoAlt as Dto;

class AltTest extends PHPUnit_Framework_Testcase
{
    public function test1()
    {
        $D = new Dto();
        $D->append('x');
        $D->append('y');
        $D->append('z');
    
        $this->assertEquals(['x','y','z'], $D->toArray());

    }
    
    public function test2()
    {
        $D = new Dto();
        $D[] = 'x';
        $D[] = 'y';
        $D[] = 'z';
    
        $this->assertEquals(['x','y','z'], $D->toArray());
    }
    
    public function test3()
    {
        $D = new Dto();
        $D->set('.', ['x','y','z']);
        $this->assertEquals(['x','y','z'], $D->toArray());
    }
    public function testPrimitiveArraySet2()
    {
        $D = new TestArrayOfIntegersTestDto2(['1a', '2b', 3.3]);
        //$this->assertEquals([1, 2, 3], $D->toArray());
        //$this->assertEquals([1, 2, 3], (array)$D);
        
        $D[] = '4d';
        $this->assertEquals([1, 2, 3, 4], $D->toArray());
    }
    
    // What about other ways to achieve this behavior?
//    public function testThatWeCanSetTheRootIndexAndAchieveTheSameResultAsPassingToTheConstructor()
//    {
//        $D = new TestArrayOfIntegersTestDto2();
//        $D->set('.', [1, 2, 3]);
//        print_r($D->toArray()); exit;
//        $this->assertEquals([1, 2, 3], $D->toArray());
//        $this->assertEquals([1, 2, 3], (array)$D);
//    }
}

class TestArrayOfIntegersTestDto2 extends \Dto\DtoAlt
{
    protected $template = [
    ];
    
    protected $meta = [
        '.' => [
            'type' => 'array',
            'values' => [
                'type' => 'integer'
            ]
        ],
    ];
}
