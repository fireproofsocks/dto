<?php

namespace DtoTest\UseCases\Hashes;

use DtoTest\TestCase;

class HashOfDtosTest extends TestCase
{
    public function testPassingArraysViaConstructorResultsInProperStructure()
    {
        $data = [
            'alpha' => ['x'=>'a',],
            'beta' => ['x'=>'d',],
            'charlie' => ['x'=>'g',],
        ];

        $D = new TestRecordSet($data);
        $this->assertEquals($data, $D->toArray());
    }

    public function testPassingArraysViaConstructorFiltersOutExtraParameters()
    {
        $data = [
            'alpha' => ['x'=>'a',],
            //'beta' => ['x'=>'d',],
            //'charlie' => ['x'=>'g',],
        ];

        $extra = $data;
        $extra['alpha']['oops'] = 'This does not belong';

        $D = new TestRecordSet($extra);

        $this->assertEquals($data, $D->toArray());
    }

    public function testEachRecordInSetIsInstanceOfChildDto()
    {
        $data = [
            'alpha' => ['x'=>'a',],
            'beta' => ['x'=>'d',],
            'charlie' => ['x'=>'g',],
        ];


        $D = new TestRecordSet($data);

        foreach ($D as $key => $value) {
            $this->assertInstanceOf(TestRecord::class, $value);
        }
    }

}

class TestRecordSet extends \Dto\Dto
{
    protected $template = [];

    protected $meta = [
        '.' => [
            'type' => 'hash',
            'anonymous' => true,
            'values' => [
                'type' => 'dto',
                'class' => 'DtoTest\UseCases\Hashes\TestRecord'
            ]
        ]
    ];
}
class TestRecord extends \Dto\Dto
{
    protected $template = [
        'x' => '',
    ];
}