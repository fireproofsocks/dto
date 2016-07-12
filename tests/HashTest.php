<?php
class HashTest extends PHPUnit_Framework_Testcase
{

}

class TestHashTestDto extends \Dto\Dto
{
    protected $template = [
        'hash' => null,
        'hash_integer' => null,
        'hash_float' => null,
        'hash_boolean' => null,
        'hash_string' => null,
        'hash_array' => null,
        'hash_hash' => null, // ??
        'hash_dto' => null,
        'hash2' => null,
    ];

    protected $definitions = [
        'hash' => [
            'type' => 'hash',
            'values' => 'string'
        ],
        'hash_integer' => [
            'type' => 'hash',
            'values' => 'integer'
        ],
        'hash_float' => [
            'type' => 'hash',
            'values' => 'float'
        ],
        'hash_boolean' => [
            'type' => 'hash',
            'values' => 'boolean'
        ],
        'hash_string' => [
            'type' => 'hash',
            'values' => 'string'
        ],
        'hash_array' => [
            'type' => 'hash',
            'values' => 'array'
        ],
        'hash_hash' => [
            'type' => 'hash',
            'values' => 'hash'
        ],
        'hash_dto' => [
            'type' => 'hash',
            'values' => 'dto',
            'class' => '',
        ],
        'hash2' => [
            'type' => 'hash',
            'template' => [
                'string' => '',
                'integer' => 0
            ]
        ],
    ];
}