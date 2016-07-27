<?php
class HashTest extends PHPUnit_Framework_Testcase
{
    public function test1()
    {
        $D = new TestHashTestDto();
        $D->x = 'y';
        $this->assertEquals(['x' => true], $D->toArray());
    }
}

class TestHashTestDto extends \Dto\Dto
{
    protected $template = [
//        'myhash' => null,
//        'hash_integer' => null,
//        'hash_float' => null,
//        'hash_boolean' => null,
//        'hash_string' => null,
//        'hash_array' => null,
//        'hash_hash' => null, // ??
//        'hash_dto' => null,
//        'hash2' => null,
    ];

    protected $meta = [
        //'*' => [ // wildcard or dot?  Only makes sense for hashes or arrays
        '.' => [
            'type' => 'hash',
            'values' => 'boolean'
        ],
//        'myhash' => [
//            'type' => 'hash',
//            'values' => 'boolean'
//        ],
//        'hash_integer' => [
//            'type' => 'hash',
//            'values' => 'integer'
//        ],
//        'hash_float' => [
//            'type' => 'hash',
//            'values' => 'float'
//        ],
//        'hash_boolean' => [
//            'type' => 'hash',
//            'values' => 'boolean'
//        ],
//        'hash_string' => [
//            'type' => 'hash',
//            'values' => 'string'
//        ],
//        'hash_array' => [
//            'type' => 'hash',
//            'values' => 'array'
//        ],
//        'hash_hash' => [
//            'type' => 'hash',
//            'values' => 'hash'
//        ],
//        'hash_dto' => [
//            'type' => 'hash',
//            'values' => 'dto',
//            'class' => '',
//        ],
//        'hash2' => [
//            'type' => 'hash',
//            'template' => [
//                'string' => '',
//                'integer' => 0
//            ]
//        ],
    ];
}