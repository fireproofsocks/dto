<?php
class HashTest extends DtoTest\TestCase
{
    public function testAllKeysAreAllowedButTheValuesMustBeBooleans()
    {
        $D = new TestHashTestDto();
        $D->x = 'y';
        $this->assertEquals(['x' => true], $D->toArray());
    }
    public function test1()
    {
        $D = new \Dto\Dto();
        $D->x(['x' => 'y']);
        print_r($D->toArray()); exit;
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