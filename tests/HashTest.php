<?php
class HashTest extends PHPUnit_Framework_Testcase
{
    public function testAllKeysAreAllowedButTheValuesMustBeBooleans()
    {
        $D = new TestHashTestDto();
        $D->x = 'y';
        $this->assertEquals(['x' => true], $D->toArray());
    }
}

class TestHashTestDto extends \Dto\Dto
{
    protected $template = [];

    protected $meta = [
        '.' => [
            'type' => 'hash',
            'values' => [
                'type' => 'boolean'
            ]
        ]
    ];
}