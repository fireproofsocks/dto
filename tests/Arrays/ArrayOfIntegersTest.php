<?php
class ArrayOfIntegersTest extends PHPUnit_Framework_Testcase
{
    
    public function testArraySet1()
    {
        $D = new TestArrayOfIntegersTestDto();
        $D->array = [1, 2, 3];
        $this->assertEquals([1, 2, 3], $D->array->toArray());
        $this->assertEquals([1, 2, 3], (array)$D->array);
        
    }
    
    public function testArraySet2()
    {
        $D = new TestArrayOfIntegersTestDto();
        $D->array = ['1a', '2b', 3.3];
        $this->assertEquals([1, 2, 3], $D->array->toArray());
        $this->assertEquals([1, 2, 3], (array)$D->array);
        
    }
    
    public function TestArrayOfIntegersTestDto()
    {
        $D = new TestArrayOfIntegersTestDto();
        $D->array = [1, 2];
        $D->array[] = 3;
        
        $this->assertEquals([1, 2, 3], $D->array->toArray());
        $this->assertEquals([1, 2, 3], (array) $D->array);
        
    }
    
    public function testArrayErraticIndex()
    {
        $D = new TestArrayOfIntegersTestDto();
        
        $erratic_index = [
            'a' => 1,
            'b' => 2,
            'c' => 3
        ];
        
        $D->array = $erratic_index;
        $this->assertEquals([1, 2, 3], $D->array->toArray());
        $this->assertEquals([1, 2, 3], (array) $D->array);
    }
    
    
    /*
     * Things are more interesting when we really treat the ArrayObject as an array primitive
     */
    public function testPrimitiveArraySet1()
    {
        $D = new TestArrayOfIntegersTestDto2([1, 2, 3]);
        $this->assertEquals([1, 2, 3], $D->toArray());
        $this->assertEquals([1, 2, 3], (array)$D);
    }
    
    public function testPrimitiveArraySet2()
    {
        $D = new TestArrayOfIntegersTestDto2(['1a', '2b', 3.3]);
        $this->assertEquals([1, 2, 3], $D->toArray());
        $this->assertEquals([1, 2, 3], (array)$D);

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

class TestArrayOfIntegersTestDto extends \Dto\Dto
{
    protected $template = [
        'array' => [],
    ];

    protected $meta = [
        'array' => [
            'type' => 'array',
            'values' => [
                'type' => 'integer'
            ]
        ],
    ];
}

class TestArrayOfIntegersTestDto2 extends \Dto\Dto
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