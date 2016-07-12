<?php
class NullableTest extends PHPUnit_Framework_Testcase
{

}

class TestNullableTestDto extends \Dto\Dto
{
    protected $template = [
        'my_nullable' => true,
        'my_not_nullable' => '',
    ];

    protected $meta = [
        'my_nullable' => [
            'type' => 'string',
            'nullable' => true
        ],
        'my_not_nullable' => [
            'type' => 'string',
            'nullable' => false
        ],
    ];
}
