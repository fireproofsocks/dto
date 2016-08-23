<?php
class ArrayOfDtoTest extends DtoTest\TestCase
{
    
    public function testPassArrayOfDtsToConstructor()
    {
        $data1 = ['x'=>'a', 'y'=>'b', 'z'=>'c'];
        $data2 = ['x'=>'d', 'y'=>'e', 'z'=>'f'];
        $data3 = ['x'=>'g', 'y'=>'h', 'z'=>'i'];
            
        $record1 = new TestRecord($data1);
        $record2 = new TestRecord($data2);
        $record3 = new TestRecord($data3);
    
        $D = new TestRecordSet([$record1,$record2,$record3]);

        $this->assertEquals([$data1, $data2, $data3], $D->toArray());
    }
    
    public function testAppendDtosToTheArrayOfDtos()
    {
        $D = new TestRecordSet();
    
    
        $data1 = ['x'=>'a', 'y'=>'b', 'z'=>'c'];
        $data2 = ['x'=>'d', 'y'=>'e', 'z'=>'f'];
        $data3 = ['x'=>'g', 'y'=>'h', 'z'=>'i'];
    
        $D[] = new TestRecord($data1);
        $D[] = new TestRecord($data2);
        $D[] = new TestRecord($data3);
    
        $this->assertEquals([$data1, $data2, $data3], $D->toArray());
    }
}

class TestRecordSet extends \Dto\Dto
{
    protected $template = [];
    
    protected $meta = [
        '.' => [
            'type' => 'array',
            'values' => [
                'type' => 'dto',
                'class' => 'TestRecord'
            ]
        ]
    ];
}
class TestRecord extends \Dto\Dto
{
    protected $template = [
        'x' => '',
        'y' => '',
        'z' => '',
    ];
}