<?php

namespace DtoTest\UseCases\Arrays;

use DtoTest\TestCase;

class ArrayOfDtoTest extends TestCase
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

    public function testPassingArraysViaConstructorFiltersOutUndefinedProperties()
    {
        $data = [
            ['x'=>'a', 'y'=>'b', 'z'=>'c'],
            ['x'=>'d', 'y'=>'e', 'z'=>'f'],
            ['x'=>'g', 'y'=>'h', 'z'=>'i'],
        ];

        $extra = $data;
        $extra[0]['a'] = 'oops';

        $D = new TestRecordSet($extra);

        $this->assertEquals($data, $D->toArray());
    }

    public function testPassingArraysViaSetMethodShouldFilterOutUndefinedProperties()
    {
        $data = [
            ['x'=>'a', 'y'=>'b', 'z'=>'c'],
            ['x'=>'d', 'y'=>'e', 'z'=>'f'],
            ['x'=>'g', 'y'=>'h', 'z'=>'i'],
        ];

        $extra = $data;
        $extra[0]['a'] = 'oops';

        $D = new TestRecordSet();
        $D->set('.', $extra);

        $this->assertEquals($data, $D->toArray());
    }

    public function testEachArrayInArrayOfDtosHasTypeOfChildDto()
    {
        $data = [
            ['x'=>'a', 'y'=>'b', 'z'=>'c'],
            ['x'=>'d', 'y'=>'e', 'z'=>'f'],
            ['x'=>'g', 'y'=>'h', 'z'=>'i'],
        ];

        $D = new TestRecordSet($data);

        foreach ($D as $record) {
            $this->assertInstanceOf(TestRecord::class, $record);
        }
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
                'class' => 'DtoTest\UseCases\Arrays\TestRecord'
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
/*
 * Dto\Exceptions\InvalidDataTypeException: .0 value must be instance of S6\DataTransfer\ProductType\ProductTypeDto
 *
 *
 *      $collection = new ProductTypeDto();
        $collection->id = $results->id;
        // etc
        foreach ($results->attributes->children as $child) {
            $tmp = new ProductTypeDto((array) $child);

            print_r($tmp->toArray()); exit;
            $collection->children[] = new ProductTypeDto((array) $child);
        }
        print_r($collection->toArray()); exit;
 */