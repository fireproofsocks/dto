<?php
class ArrayTest extends PHPUnit_Framework_Testcase
{

    public function testArray()
    {
        $D = new TestArrayTestDto();
        $D->array = ['x','y','z'];
        $this->assertEquals(['x','y','z'], $D->array->toArray());
        $this->assertEquals(['x','y','z'], (array) $D->array);

        $D->array = ['x','y'];
        $D->array[] = 'z';
        $this->assertEquals(['x','y','z'], $D->array->toArray());
        $this->assertEquals(['x','y','z'], (array) $D->array);

        $erratic_index = [
            'a' => 'apple',
            'b' => 'banana',
            'c' => 'cherry'
        ];

        $D->array = $erratic_index;
        $this->assertEquals(['apple','banana','cherry'], $D->array->toArray());
        $this->assertEquals(['apple','banana','cherry'], (array) $D->array);
    }

    public function testArrayOfString()
    {
        $D = new TestArrayTestDto();
//        $D->array = ['a','b','c'];
//        $this->assertEquals(['a','b','c'], $D->array);
    }

    public function testEnsureMetaDataGetsAllDefaults()
    {
        // if we define "type", the meta data should get the default values for "callback" as well
    }
}

class TestArrayTestDto extends \Dto\Dto
{
    protected $template = [
        'array' => [],
//        'array_integer' => [],
//        'array_float' => [],
//        'array_boolean' => [],
//        'array_string' => [],
//        'array_array' => [],
    ];

    protected $meta = [
        'array' => [
            'type' => 'array',
            'values' => 'string'
        ],
//        'array_integer' => [
//            'type' => 'array',
//            'values' => 'integer'
//        ],
//        'array_float' => [
//            'type' => 'array',
//            'values' => 'float'
//        ],
//        'array_boolean' => [
//            'type' => 'array',
//            'values' => 'boolean'
//        ],
//        'array_string' => [
//            'type' => 'array',
//            'values' => 'string'
//        ],
//        'array_array' => [
//            'type' => 'array',
//            'values' => 'array'
//        ]
    ];
}